<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
$db_conn = require __DIR__ . '/../includes/db.php';

$app = AppFactory::create();
$app->setBasePath(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
$app->addBodyParsingMiddleware();

function jsonResponse(Response $response, array $payload, int $status = 200): Response
{
    $response->getBody()->write(json_encode($payload));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}

$app->get('/api/health', function (Request $request, Response $response) {
    $payload = [
        'status' => 'ok',
        'app' => 'Universal Sambal API',
    ];

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/menu', function (Request $request, Response $response) {
    $payload = [
        'message' => 'Menu API placeholder. Connect this route to the menus table later.',
        'items' => [],
    ];

    return jsonResponse($response, $payload);
});

$app->post('/api/login', function (Request $request, Response $response) use ($db_conn) {
    $data = (array) $request->getParsedBody();
    $identifier = trim($data['name'] ?? $data['user_id'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($identifier === '' || $password === '') {
        return jsonResponse($response, [
            'error' => 'Name or user ID and password are required.',
        ], 422);
    }

    try {
        $stmt = $db_conn->prepare(
            'SELECT user_id, name, role, phone FROM users
             WHERE (name = :identifier OR user_id = :identifier) AND password = :password
             LIMIT 1'
        );
        $stmt->execute([
            'identifier' => $identifier,
            'password' => $password,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return jsonResponse($response, [
                'error' => 'Invalid login credentials.',
            ], 401);
        }

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

$app->post('/api/orders', function (Request $request, Response $response) {
    $payload = [
        'message' => 'Order creation placeholder. Cart checkout will post here later.',
    ];

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(501);
});

$app->get('/api/orders/{order_id}', function (Request $request, Response $response, array $args) {
    $payload = [
        'message' => 'Order tracking placeholder.',
        'order_id' => $args['order_id'],
    ];

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
