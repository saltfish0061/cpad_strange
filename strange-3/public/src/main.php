<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';
$db_conn = require __DIR__ . '/../libs/db_connect_PDO_SLIM.php';

$app = AppFactory::create();

// Support both root .htaccess routing to src/main.php and the legacy api/index.php entry.
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
if (str_ends_with($scriptName, '/src/main.php')) {
    $basePath = substr($scriptName, 0, -strlen('/src/main.php'));
} elseif (str_ends_with($scriptName, '/api/index.php')) {
    $basePath = substr($scriptName, 0, -strlen('/api/index.php'));
} else {
    $basePath = rtrim(dirname($scriptName), '/');
}

if ($basePath !== '' && $basePath !== '.') {
    $app->setBasePath($basePath);
}

$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->getDefaultErrorHandler()->forceContentType('application/json');

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

    $decoded = json_decode((string) $request->getBody(), true);
    return is_array($decoded) ? $decoded : [];
}

function nextMenuItemId(PDO $db, string $category): string
{
    $prefix = $category === 'drink' ? 'D' : 'F';
    $stmt = $db->prepare("SELECT item_id FROM menus WHERE item_id LIKE ? ORDER BY item_id DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $lastId = $stmt->fetchColumn();
    $nextNumber = $lastId ? ((int) substr($lastId, 1)) + 1 : 1;

    return $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
}

function nextCustomerId(PDO $db): string
{
    $stmt = $db->query("SELECT user_id FROM users WHERE user_id LIKE 'C%' ORDER BY CAST(SUBSTRING(user_id, 2) AS UNSIGNED) DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();
    $nextNumber = $lastId ? ((int) substr($lastId, 1)) + 1 : 1;

    return 'C' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
}

function validateMenuPayload(array $body): array
{
    $errors = [];
    $name = trim((string) ($body['name'] ?? ''));
    $description = trim((string) ($body['description'] ?? ''));
    $price = $body['price'] ?? null;
    $category = strtolower(trim((string) ($body['category'] ?? '')));

    if ($name === '') {
        $errors[] = 'Menu item name is required.';
    }

    if (!is_numeric($price) || (float) $price <= 0) {
        $errors[] = 'Price must be greater than 0.';
    }

    if (!in_array($category, ['food', 'drink'], true)) {
        $errors[] = 'Category must be food or drink.';
    }

    return [$errors, [
        'name' => $name,
        'description' => $description,
        'price' => is_numeric($price) ? (float) $price : 0,
        'category' => $category,
    ]];
}

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
        $params = $request->getQueryParams();
        $includeUnavailable = (string) ($params['include_unavailable'] ?? '') === '1';

        if ($includeUnavailable) {
            $stmt = $db_conn->prepare("SELECT * FROM menus ORDER BY is_available DESC, category, name");
            $stmt->execute();
        } else {
            $stmt = $db_conn->prepare("SELECT * FROM menus WHERE is_available = 1 ORDER BY category, name");
            $stmt->execute();
        }
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

$app->post('/api/login', function (Request $request, Response $response) use ($db_conn) {
    $data = (array) $request->getParsedBody();
    $identifier = trim($data['username'] ?? $data['name'] ?? $data['user_id'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($identifier === '' || $password === '') {
        return jsonResponse($response, [
            'error' => 'Username and password are required.',
        ], 422);
    }

    try {
        $stmt = $db_conn->prepare(
            'SELECT user_id, name, password, role, phone, address FROM users
             WHERE BINARY name = :identifier
             LIMIT 1'
        );
        $stmt->execute([
            'identifier' => $identifier,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $password !== $user['password']) {
            return jsonResponse($response, [
                'error' => 'Username or password is wrong.',
            ], 401);
        }

        unset($user['password']);

        return jsonResponse($response, [
            'message' => 'Login successful.',
            'user' => $user,
        ]);
    } catch (Throwable $error) {
        return jsonResponse($response, [
            'error' => 'Unable to login right now.',
        ], 500);
    }
});

$app->post('/api/register', function (Request $request, Response $response) use ($db_conn) {
    $body = parsedBody($request);
    $name = trim((string) ($body['username'] ?? $body['name'] ?? ''));
    $phone = trim((string) ($body['phone'] ?? ''));
    $address = trim((string) ($body['address'] ?? ''));
    $password = trim((string) ($body['password'] ?? ''));
    $confirmPassword = trim((string) ($body['confirm_password'] ?? ''));
    $errors = [];

    if ($name === '') {
        $errors[] = 'Username is required.';
    }

    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    }

    if ($address === '') {
        $errors[] = 'Address is required.';
    }

    if (strlen($password) < 4) {
        $errors[] = 'Password must be at least 4 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if ($errors) {
        return jsonResponse($response, [
            'status' => 'error',
            'errors' => $errors,
        ], 422);
    }

    try {
        $duplicateUsername = $db_conn->prepare('SELECT COUNT(*) FROM users WHERE name = ?');
        $duplicateUsername->execute([$name]);
        if ((int) $duplicateUsername->fetchColumn() > 0) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Username already exists.',
            ], 409);
        }

        $duplicatePhone = $db_conn->prepare('SELECT COUNT(*) FROM users WHERE phone = ?');
        $duplicatePhone->execute([$phone]);
        if ((int) $duplicatePhone->fetchColumn() > 0) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'An account with this phone already exists.',
            ], 409);
        }

        $userId = nextCustomerId($db_conn);
        $stmt = $db_conn->prepare(
            "INSERT INTO users (user_id, name, password, role, phone, address)
             VALUES (?, ?, ?, 'customer', ?, ?)"
        );
        $stmt->execute([$userId, $name, $password, $phone, $address]);

        $userStmt = $db_conn->prepare('SELECT user_id, name, role, phone, address FROM users WHERE user_id = ? LIMIT 1');
        $userStmt->execute([$userId]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Registration successful.',
            'user' => $userStmt->fetch(PDO::FETCH_ASSOC),
        ], 201);
    } catch (Throwable $error) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => 'Unable to register right now.',
        ], 500);
    }
});

$app->get('/api/profile/{user_id}', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $stmt = $db_conn->prepare('SELECT user_id, name, role, phone, address FROM users WHERE user_id = ? LIMIT 1');
        $stmt->execute([$args['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Profile not found.',
            ], 404);
        }

        return jsonResponse($response, [
            'status' => 'success',
            'user' => $user,
        ]);
    } catch (Throwable $error) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => 'Unable to load profile.',
        ], 500);
    }
});

$app->put('/api/profile/{user_id}', function (Request $request, Response $response, array $args) use ($db_conn) {
    $body = parsedBody($request);
    $name = trim((string) ($body['name'] ?? ''));
    $phone = trim((string) ($body['phone'] ?? ''));
    $address = trim((string) ($body['address'] ?? ''));
    $errors = [];

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($phone === '') {
        $errors[] = 'Phone number is required.';
    }

    if ($address === '') {
        $errors[] = 'Address is required.';
    }

    if ($errors) {
        return jsonResponse($response, [
            'status' => 'error',
            'errors' => $errors,
        ], 422);
    }

    try {
        $exists = $db_conn->prepare('SELECT COUNT(*) FROM users WHERE user_id = ?');
        $exists->execute([$args['user_id']]);

        if ((int) $exists->fetchColumn() === 0) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Profile not found.',
            ], 404);
        }

        $stmt = $db_conn->prepare('UPDATE users SET name = ?, phone = ?, address = ? WHERE user_id = ?');
        $stmt->execute([$name, $phone, $address, $args['user_id']]);

        $profileStmt = $db_conn->prepare('SELECT user_id, name, role, phone, address FROM users WHERE user_id = ? LIMIT 1');
        $profileStmt->execute([$args['user_id']]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Profile updated.',
            'user' => $profileStmt->fetch(PDO::FETCH_ASSOC),
        ]);
    } catch (Throwable $error) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => 'Unable to update profile.',
        ], 500);
    }
});

$app->patch('/api/profile/{user_id}/password', function (Request $request, Response $response, array $args) use ($db_conn) {
    $body = parsedBody($request);
    $oldPassword = trim((string) ($body['old_password'] ?? ''));
    $newPassword = trim((string) ($body['new_password'] ?? ''));
    $confirmPassword = trim((string) ($body['confirm_password'] ?? ''));
    $errors = [];

    if ($oldPassword === '') {
        $errors[] = 'Old password is required.';
    }

    if (strlen($newPassword) < 4) {
        $errors[] = 'New password must be at least 4 characters.';
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New passwords do not match.';
    }

    if ($errors) {
        return jsonResponse($response, [
            'status' => 'error',
            'errors' => $errors,
        ], 422);
    }

    try {
        $stmt = $db_conn->prepare('SELECT password FROM users WHERE user_id = ? LIMIT 1');
        $stmt->execute([$args['user_id']]);
        $currentPassword = $stmt->fetchColumn();

        if ($currentPassword === false) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Profile not found.',
            ], 404);
        }

        if ($oldPassword !== $currentPassword) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Old password is incorrect.',
            ], 422);
        }

        $update = $db_conn->prepare('UPDATE users SET password = ? WHERE user_id = ?');
        $update->execute([$newPassword, $args['user_id']]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Password updated.',
        ]);
    } catch (Throwable $error) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => 'Unable to update password.',
        ], 500);
    }
});

$app->get('/api/orders', function (Request $request, Response $response) use ($db_conn) {
    try {
        $params = $request->getQueryParams();
        $user_id = trim((string) ($params['user_id'] ?? ''));

        if ($user_id === '') {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'User ID is required.',
            ], 422);
        }

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
        $params = $request->getQueryParams();
        $user_id = trim((string) ($params['user_id'] ?? ''));

        if ($user_id === '') {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'User ID is required.',
            ], 422);
        }

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
        $user_id = trim((string) ($body['user_id'] ?? ''));
        $order_note = trim((string) ($body['order_note'] ?? ''));
        $delivery_method = strtolower(trim((string) ($body['delivery_method'] ?? 'pickup')));

        if (!in_array($delivery_method, ['pickup', 'delivery'], true)) {
            $delivery_method = 'pickup';
        }

        if (empty($items)) {
            throw new \Exception("No items in the order.");
        }

        if ($user_id === '') {
            throw new \Exception("Login is required to place an order.");
        }

        $db_conn->beginTransaction();

        $userCheck = $db_conn->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
        $userCheck->execute([$user_id]);
        if ((int) $userCheck->fetchColumn() === 0) {
            throw new \Exception("Customer profile was not found.");
        }

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

        $stmt = $db_conn->prepare("INSERT INTO orders (order_id, user_id, total_amount, status, delivery_method, order_note) VALUES (?, ?, ?, 'pending', ?, ?)");
        $stmt->execute([$order_id, $user_id, $total_amount, $delivery_method, $order_note]);

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

$app->get('/api/vendor/menu', function (Request $request, Response $response) use ($db_conn) {
    try {
        $stmt = $db_conn->query("SELECT item_id, name, description, price, category, is_available FROM menus ORDER BY category, item_id");

        return jsonResponse($response, [
            'status' => 'success',
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->post('/api/vendor/menu', function (Request $request, Response $response) use ($db_conn) {
    try {
        $body = parsedBody($request);
        [$errors, $item] = validateMenuPayload($body);

        if ($errors) {
            return jsonResponse($response, ['status' => 'error', 'errors' => $errors], 422);
        }

        $itemId = trim((string) ($body['item_id'] ?? ''));
        if ($itemId === '') {
            $itemId = nextMenuItemId($db_conn, $item['category']);
        }

        $stmt = $db_conn->prepare(
            "INSERT INTO menus (item_id, name, description, price, category, is_available)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $itemId,
            $item['name'],
            $item['description'],
            $item['price'],
            $item['category'],
            isset($body['is_available']) ? (int) (bool) $body['is_available'] : 1,
        ]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Menu item created.',
            'item_id' => $itemId,
        ], 201);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->put('/api/vendor/menu/{item_id}', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $body = parsedBody($request);
        [$errors, $item] = validateMenuPayload($body);

        if ($errors) {
            return jsonResponse($response, ['status' => 'error', 'errors' => $errors], 422);
        }

        $stmt = $db_conn->prepare(
            "UPDATE menus
             SET name = ?, description = ?, price = ?, category = ?
             WHERE item_id = ?"
        );
        $stmt->execute([
            $item['name'],
            $item['description'],
            $item['price'],
            $item['category'],
            $args['item_id'],
        ]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Menu item updated.',
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->patch('/api/vendor/menu/{item_id}/availability', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $body = parsedBody($request);
        $isAvailable = isset($body['is_available']) ? (int) (bool) $body['is_available'] : 0;

        $stmt = $db_conn->prepare("UPDATE menus SET is_available = ? WHERE item_id = ?");
        $stmt->execute([$isAvailable, $args['item_id']]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => $isAvailable ? 'Menu item marked available.' : 'Menu item marked sold out.',
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->delete('/api/vendor/menu/{item_id}', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $check = $db_conn->prepare("SELECT COUNT(*) FROM order_items WHERE item_id = ?");
        $check->execute([$args['item_id']]);

        if ((int) $check->fetchColumn() > 0) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'This item already appears in orders. Mark it sold out instead to keep sales records intact.',
            ], 409);
        }

        $stmt = $db_conn->prepare("DELETE FROM menus WHERE item_id = ?");
        $stmt->execute([$args['item_id']]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Menu item deleted.',
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->get('/api/vendor/orders', function (Request $request, Response $response) use ($db_conn) {
    try {
        $stmt = $db_conn->query(
            "SELECT o.order_id, o.user_id, u.name AS customer_name, u.phone AS customer_phone,
                    o.total_amount, o.status, o.delivery_method, o.order_note, o.order_date,
                    COUNT(oi.order_item_id) AS item_count
             FROM orders o
             JOIN users u ON u.user_id = o.user_id
             LEFT JOIN order_items oi ON oi.order_id = o.order_id
             GROUP BY o.order_id, o.user_id, u.name, u.phone, o.total_amount, o.status, o.delivery_method, o.order_note, o.order_date
             ORDER BY o.order_date DESC, o.order_id DESC"
        );

        return jsonResponse($response, [
            'status' => 'success',
            'orders' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->get('/api/vendor/orders/{order_id}', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $orderStmt = $db_conn->prepare(
            "SELECT o.order_id, o.user_id, u.name AS customer_name, u.phone AS customer_phone,
                    u.address AS customer_address, o.total_amount, o.status, o.delivery_method,
                    o.order_note, o.order_date
             FROM orders o
             JOIN users u ON u.user_id = o.user_id
             WHERE o.order_id = ?"
        );
        $orderStmt->execute([$args['order_id']]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Order not found.',
            ], 404);
        }

        $itemsStmt = $db_conn->prepare(
            "SELECT oi.order_item_id, oi.item_id, m.name, m.category, oi.quantity, oi.subtotal
             FROM order_items oi
             JOIN menus m ON m.item_id = oi.item_id
             WHERE oi.order_id = ?
             ORDER BY oi.order_item_id"
        );
        $itemsStmt->execute([$args['order_id']]);

        return jsonResponse($response, [
            'status' => 'success',
            'order' => $order,
            'items' => $itemsStmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->patch('/api/vendor/orders/{order_id}/status', function (Request $request, Response $response, array $args) use ($db_conn) {
    try {
        $body = parsedBody($request);
        $status = strtolower(trim((string) ($body['status'] ?? '')));
        $allowedStatuses = ['pending', 'preparing', 'ready', 'on_the_way', 'completed', 'cancelled'];

        if (!in_array($status, $allowedStatuses, true)) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Invalid order status.',
            ], 422);
        }

        $stmt = $db_conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $args['order_id']]);

        return jsonResponse($response, [
            'status' => 'success',
            'message' => 'Order status updated.',
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->get('/api/vendor/sales', function (Request $request, Response $response) use ($db_conn) {
    try {
        $summary = $db_conn->query(
            "SELECT COUNT(*) AS completed_orders, COALESCE(SUM(total_amount), 0) AS total_sales
             FROM orders
             WHERE status = 'completed'"
        )->fetch(PDO::FETCH_ASSOC);

        $popularStmt = $db_conn->query(
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

        $statusStmt = $db_conn->query(
            "SELECT status, COUNT(*) AS total
             FROM orders
             GROUP BY status
             ORDER BY FIELD(status, 'pending', 'preparing', 'ready', 'on_the_way', 'completed', 'cancelled')"
        );

        return jsonResponse($response, [
            'status' => 'success',
            'summary' => $summary,
            'popular_items' => $popularStmt->fetchAll(PDO::FETCH_ASSOC),
            'status_counts' => $statusStmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    } catch (\Exception $e) {
        return jsonResponse($response, [
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

$app->run();
