<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

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

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json');
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
