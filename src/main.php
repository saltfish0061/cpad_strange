<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->getDefaultErrorHandler()->forceContentType('application/json');

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($basePath !== '' && $basePath !== '.') {
    $app->setBasePath($basePath);
}

function db(): PDO
{
    return require __DIR__ . '/../includes/db.php';
}

function jsonResponse(Response $response, array $payload, int $status = 200): Response
{
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}

function parsedBody(Request $request): array
{
    $body = $request->getParsedBody();
    if (is_array($body)) {
        return $body;
    }

    $raw = (string) $request->getBody();
    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

function nextMenuItemId(PDO $db, string $category): string
{
    $prefix = $category === 'drink' ? 'D' : 'F';
    $stmt = $db->prepare('SELECT item_id FROM menus WHERE item_id LIKE ? ORDER BY item_id DESC LIMIT 1');
    $stmt->execute([$prefix . '%']);
    $lastId = $stmt->fetchColumn();
    $nextNumber = $lastId ? ((int) substr($lastId, 1)) + 1 : 1;

    return $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
}

function validateMenuPayload(array $body, bool $isUpdate = false): array
{
    $errors = [];
    $name = trim((string) ($body['name'] ?? ''));
    $description = trim((string) ($body['description'] ?? ''));
    $price = $body['price'] ?? null;
    $category = strtolower(trim((string) ($body['category'] ?? '')));

    if (!$isUpdate || array_key_exists('name', $body)) {
        if ($name === '') {
            $errors[] = 'Menu item name is required.';
        }
    }

    if (!$isUpdate || array_key_exists('price', $body)) {
        if (!is_numeric($price) || (float) $price <= 0) {
            $errors[] = 'Price must be greater than 0.';
        }
    }

    if (!$isUpdate || array_key_exists('category', $body)) {
        if (!in_array($category, ['food', 'drink'], true)) {
            $errors[] = 'Category must be food or drink.';
        }
    }

    return [$errors, [
        'name' => $name,
        'description' => $description,
        'price' => is_numeric($price) ? (float) $price : 0,
        'category' => $category,
    ]];
}

$app->get('/health', function (Request $request, Response $response) {
    return jsonResponse($response, [
        'status' => 'ok',
        'app' => 'Universal Sambal API',
    ]);
});

$app->get('/menu', function (Request $request, Response $response) {
    $stmt = db()->query('SELECT item_id, name, description, price, category, is_available FROM menus ORDER BY category, item_id');

    return jsonResponse($response, [
        'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
});

$app->post('/orders', function (Request $request, Response $response) {
    return jsonResponse($response, [
        'message' => 'Order creation placeholder. Cart checkout will post here later.',
    ], 501);
});

$app->get('/orders/{order_id}', function (Request $request, Response $response, array $args) {
    return jsonResponse($response, [
        'message' => 'Order tracking placeholder.',
        'order_id' => $args['order_id'],
    ]);
});

$app->get('/vendor/menu', function (Request $request, Response $response) {
    $stmt = db()->query('SELECT item_id, name, description, price, category, is_available FROM menus ORDER BY category, item_id');

    return jsonResponse($response, [
        'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
});

$app->post('/vendor/menu', function (Request $request, Response $response) {
    $body = parsedBody($request);
    [$errors, $item] = validateMenuPayload($body);

    if ($errors) {
        return jsonResponse($response, ['errors' => $errors], 422);
    }

    $db = db();
    $itemId = trim((string) ($body['item_id'] ?? ''));
    if ($itemId === '') {
        $itemId = nextMenuItemId($db, $item['category']);
    }

    try {
        $stmt = $db->prepare(
            'INSERT INTO menus (item_id, name, description, price, category, is_available)
             VALUES (:item_id, :name, :description, :price, :category, :is_available)'
        );
        $stmt->execute([
            ':item_id' => $itemId,
            ':name' => $item['name'],
            ':description' => $item['description'],
            ':price' => $item['price'],
            ':category' => $item['category'],
            ':is_available' => isset($body['is_available']) ? (int) (bool) $body['is_available'] : 1,
        ]);
    } catch (PDOException $e) {
        return jsonResponse($response, ['errors' => ['Menu item could not be created. The item ID may already exist.']], 409);
    }

    return jsonResponse($response, [
        'message' => 'Menu item created.',
        'item_id' => $itemId,
    ], 201);
});

$app->put('/vendor/menu/{item_id}', function (Request $request, Response $response, array $args) {
    $body = parsedBody($request);
    [$errors, $item] = validateMenuPayload($body);

    if ($errors) {
        return jsonResponse($response, ['errors' => $errors], 422);
    }

    $stmt = db()->prepare(
        'UPDATE menus
         SET name = :name, description = :description, price = :price, category = :category
         WHERE item_id = :item_id'
    );
    $stmt->execute([
        ':name' => $item['name'],
        ':description' => $item['description'],
        ':price' => $item['price'],
        ':category' => $item['category'],
        ':item_id' => $args['item_id'],
    ]);

    if ($stmt->rowCount() === 0) {
        return jsonResponse($response, ['errors' => ['Menu item not found or unchanged.']], 404);
    }

    return jsonResponse($response, ['message' => 'Menu item updated.']);
});

$app->patch('/vendor/menu/{item_id}/availability', function (Request $request, Response $response, array $args) {
    $body = parsedBody($request);
    $isAvailable = isset($body['is_available']) ? (int) (bool) $body['is_available'] : 0;

    $stmt = db()->prepare('UPDATE menus SET is_available = :is_available WHERE item_id = :item_id');
    $stmt->execute([
        ':is_available' => $isAvailable,
        ':item_id' => $args['item_id'],
    ]);

    if ($stmt->rowCount() === 0) {
        return jsonResponse($response, ['errors' => ['Menu item not found or unchanged.']], 404);
    }

    return jsonResponse($response, [
        'message' => $isAvailable ? 'Menu item marked available.' : 'Menu item marked sold out.',
    ]);
});

$app->delete('/vendor/menu/{item_id}', function (Request $request, Response $response, array $args) {
    $db = db();
    $check = $db->prepare('SELECT COUNT(*) FROM order_items WHERE item_id = :item_id');
    $check->execute([':item_id' => $args['item_id']]);

    if ((int) $check->fetchColumn() > 0) {
        return jsonResponse($response, [
            'errors' => ['This item already appears in orders. Mark it sold out instead to keep sales records intact.'],
        ], 409);
    }

    $stmt = $db->prepare('DELETE FROM menus WHERE item_id = :item_id');
    $stmt->execute([':item_id' => $args['item_id']]);

    if ($stmt->rowCount() === 0) {
        return jsonResponse($response, ['errors' => ['Menu item not found.']], 404);
    }

    return jsonResponse($response, ['message' => 'Menu item deleted.']);
});

$app->get('/vendor/orders', function (Request $request, Response $response) {
    $stmt = db()->query(
        'SELECT o.order_id, o.user_id, u.name AS customer_name, u.phone AS customer_phone,
                o.total_amount, o.status, o.order_date,
                COUNT(oi.order_item_id) AS item_count
         FROM orders o
         JOIN users u ON u.user_id = o.user_id
         LEFT JOIN order_items oi ON oi.order_id = o.order_id
         GROUP BY o.order_id, o.user_id, u.name, u.phone, o.total_amount, o.status, o.order_date
         ORDER BY o.order_date DESC, o.order_id DESC'
    );

    return jsonResponse($response, [
        'orders' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
});

$app->get('/vendor/orders/{order_id}', function (Request $request, Response $response, array $args) {
    $db = db();
    $orderStmt = $db->prepare(
        'SELECT o.order_id, o.user_id, u.name AS customer_name, u.phone AS customer_phone,
                o.total_amount, o.status, o.order_date
         FROM orders o
         JOIN users u ON u.user_id = o.user_id
         WHERE o.order_id = :order_id'
    );
    $orderStmt->execute([':order_id' => $args['order_id']]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        return jsonResponse($response, ['errors' => ['Order not found.']], 404);
    }

    $itemsStmt = $db->prepare(
        'SELECT oi.order_item_id, oi.item_id, m.name, m.category, oi.quantity, oi.subtotal
         FROM order_items oi
         JOIN menus m ON m.item_id = oi.item_id
         WHERE oi.order_id = :order_id
         ORDER BY oi.order_item_id'
    );
    $itemsStmt->execute([':order_id' => $args['order_id']]);

    return jsonResponse($response, [
        'order' => $order,
        'items' => $itemsStmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
});

$app->patch('/vendor/orders/{order_id}/status', function (Request $request, Response $response, array $args) {
    $body = parsedBody($request);
    $status = strtolower(trim((string) ($body['status'] ?? '')));
    $allowedStatuses = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];

    if (!in_array($status, $allowedStatuses, true)) {
        return jsonResponse($response, ['errors' => ['Invalid order status.']], 422);
    }

    $stmt = db()->prepare('UPDATE orders SET status = :status WHERE order_id = :order_id');
    $stmt->execute([
        ':status' => $status,
        ':order_id' => $args['order_id'],
    ]);

    if ($stmt->rowCount() === 0) {
        return jsonResponse($response, ['errors' => ['Order not found or unchanged.']], 404);
    }

    return jsonResponse($response, ['message' => 'Order status updated.']);
});

$app->get('/vendor/sales', function (Request $request, Response $response) {
    $db = db();

    $summary = $db->query(
        "SELECT COUNT(*) AS completed_orders, COALESCE(SUM(total_amount), 0) AS total_sales
         FROM orders
         WHERE status = 'completed'"
    )->fetch(PDO::FETCH_ASSOC);

    $popularStmt = $db->query(
        "SELECT m.item_id, m.name, m.category,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.subtotal) AS total_sales
         FROM order_items oi
         JOIN orders o ON o.order_id = oi.order_id
         JOIN menus m ON m.item_id = oi.item_id
         WHERE o.status = 'completed'
         GROUP BY m.item_id, m.name, m.category
         ORDER BY total_quantity DESC, total_sales DESC
         LIMIT 5"
    );

    $statusStmt = $db->query(
        'SELECT status, COUNT(*) AS total
         FROM orders
         GROUP BY status
         ORDER BY FIELD(status, "pending", "preparing", "ready", "completed", "cancelled")'
    );

    return jsonResponse($response, [
        'summary' => $summary,
        'popular_items' => $popularStmt->fetchAll(PDO::FETCH_ASSOC),
        'status_counts' => $statusStmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
});

$app->run();
