<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$container = $app->getContainer();

$container['condif'] = function ($c) {
    return new \Noodlehaus\Config([
        __DIR__ . '/../config/cache.php',
    ]);
};

$container['cache'] = function ($c) {
    $client = new Predis\Client([
        'scheme'   => 'tcp',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'passowrd' => null,
    ]);
    
    return new \App\Cache\RedisAdapter($client);
};

$app->get('/', function ($request, $response) {
    $users = $this->cache->remember('users', 10, function () {
        $users = [
            ['username' => 'alex', 'email' => 'alex@example.com'],
            ['username' => 'hamoud', 'email' => 'h.alhoqbani@example.com'],
            ['username' => 'Hamoud Alhoqbani', 'email' => 'hamoud@example.com'],
        ];
        
        return json_encode($users);
    });
    
    return $response->withHeader('Content-Type', 'application/json')->write($users);
//    return $response->withJson(json_decode($users));
});