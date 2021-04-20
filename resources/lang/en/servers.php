<?php

return [

    'index' => [

        'title' => 'Servers',

        'table' => [
            'server' => 'Server',
            'ip' => 'IP Address',
        ],

    ],

    'create' => [

        'title' => 'Provision New Server',

    ],

    'types' => [
        'app' => [
            'name' => 'App Server',
        ],
        'web' => [
            'name' => 'Web Server',
        ],
        'worker' => [
            'name' => 'Worker Server',
        ],
        'database' => [
            'name' => 'Database Server',
        ],
        'cache' => [
            'name' => 'Cache Server',
        ],
    ],
];
