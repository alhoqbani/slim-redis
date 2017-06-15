<?php
return [
    'config' => [
        'drivers'     => 'redis',
        'connections' => [
            'redis' => [
                'scheme'   => 'tcp',
                'host'     => '127.0.0.1',
                'port'     => 6379,
                'passowrd' => null,
            ],
        ],
    ],
];