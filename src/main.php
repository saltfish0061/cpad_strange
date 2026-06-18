<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
$db_conn = require __DIR__ . '/../includes/db.php';

$app = AppFactory::create();

// Set appropriate base path for Slim 4 under subdirectory
$basePath = str_replace('/src/main.php', '', $_SERVER['SCRIPT_NAME']);
$app->setBasePath($basePath);

$app->addBodyParsingMiddleware();

$app->get('/api/health', function (Request $request, Response $response) {
    $payload = [
        'status' => 'ok',
        'app' => 'Universal Sambal API',
    ];

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/menu', function (Request $request, Response $response) use ($db_conn) {
    try {
        $stmt = $db_conn->prepare("SELECT * FROM menus WHERE is_available = 1 ORDER BY category, name");
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'items' => $items
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->get('/api/orders', function (Request $request, Response $response) use ($db_conn) {
    try {
        $user_id = 'C001'; // Mock customer session
        $stmt = $db_conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'orders' => $orders
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->get('/api/orders/{order_id}', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $order_id = $args['order_id'];
        $user_id = 'C001'; // Mock customer session
        
        $stmt = $db_conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Order not found'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $stmt_items = $db_conn->prepare("
            SELECT oi.*, m.name as item_name, m.price as unit_price 
            FROM order_items oi
            JOIN menus m ON oi.item_id = m.item_id
            WHERE oi.order_id = ?
        ");
        $stmt_items->execute([$order_id]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'order' => $order,
            'items' => $items
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->post('/api/orders', function (Request $request, Response $response) use ($db_conn) {
    try {
        $body = $request->getParsedBody();
        $items = $body['items'] ?? [];
        
        if (empty($items)) {
            throw new \Exception("No items in the order.");
        }
        
        $user_id = 'C001'; // Mock customer session
        
        $db_conn->beginTransaction();
        
        $total_amount = 0;
        $items_to_save = [];
        foreach ($items as $item_id => $quantity) {
            if ($quantity <= 0) {
                continue;
            }
            $stmt = $db_conn->prepare("SELECT price FROM menus WHERE item_id = ? AND is_available = 1");
            $stmt->execute([$item_id]);
            $price = $stmt->fetchColumn();
            
            if ($price === false) {
                throw new \Exception("Item " . $item_id . " is not available or does not exist.");
            }
            
            $subtotal = $price * $quantity;
            $total_amount += $subtotal;
            $items_to_save[] = [
                'item_id' => $item_id,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
        
        if (empty($items_to_save)) {
            throw new \Exception("No valid items to order.");
        }
        
        $stmt = $db_conn->query("SELECT order_id FROM orders WHERE order_id LIKE 'O%' ORDER BY CAST(SUBSTRING(order_id, 2) AS UNSIGNED) DESC LIMIT 1");
        $last_id = $stmt->fetchColumn();
        $order_id = $last_id ? sprintf("O%03d", (int)substr($last_id, 1) + 1) : "O001";
        
        $stmt = $db_conn->prepare("INSERT INTO orders (order_id, user_id, total_amount, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$order_id, $user_id, $total_amount]);
        
        $stmt = $db_conn->query("SELECT order_item_id FROM order_items WHERE order_item_id LIKE 'OI%' ORDER BY CAST(SUBSTRING(order_item_id, 3) AS UNSIGNED) DESC LIMIT 1");
        $last_oi_id = $stmt->fetchColumn();
        $oi_num = $last_oi_id ? (int)substr($last_oi_id, 2) : 0;
        
        $stmt_item = $db_conn->prepare("INSERT INTO order_items (order_item_id, order_id, item_id, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($items_to_save as $item) {
            $oi_num++;
            $oi_id = sprintf("OI%03d", $oi_num);
            $stmt_item->execute([$oi_id, $order_id, $item['item_id'], $item['quantity'], $item['subtotal']]);
        }
        
        $db_conn->commit();
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'order_id' => $order_id
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        if ($db_conn->inTransaction()) {
            $db_conn->rollBack();
        }
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
});

$app->run();

