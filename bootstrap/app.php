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

$container['db'] = function ($c) {
    return new \PDO('mysql:dbname=slim_redis;host=127.0.0.1', 'root');
};

$container['http'] = function ($c) {
    return new \GuzzleHttp\Client();
};

$app->get('/users', function ($request, $response) {
    $users = $this->cache->remember('users', 10, function () {
        $users = $this->db->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode($users);
    });
    
    return $response->withHeader('Content-Type', 'application/json')->write($users);
});

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

$app->get('/hn', function ($request, $response) {
    /** @var GuzzleHttp\Client $client */
    $client = $this->http;
    
    $stories = $this->cache->remember('hn:top-stories', 10, function () use ($client) {
        $res = $client->get('https://hacker-news.firebaseio.com/v0/topstories.json');
        $stories = [];
        foreach (array_slice(json_decode($res->getBody()), 0, 15) as $story) {
            $res = $client->get('https://hacker-news.firebaseio.com/v0/item/' . $story . '.json');
            $stories[] = json_decode($res->getBody());
        };
        return json_encode($stories);
    });
    
    return $response->withHeader('Content-Type', 'application/json')->write($stories);
});