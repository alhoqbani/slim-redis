<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new Predis\Client([
    'scheme'   => 'tcp',
    'host'     => '127.0.0.1',
    'port'     => 6379,
    'passowrd' => null,
]);

//$client->set('name', 'Hamoud');

$cache = new \App\Cache\RedisAdapter($client);

$name = $cache->put('name', 'alex', 0.1);

dump($name);