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
            'description' => 'Application servers include everything you need to deploy your Python / Django application. If you don\'t want to install a database, you may disable its installation below.',
        ],
        'web' => [
            'name' => 'Web Server',
            'description' => '',
        ],
        'worker' => [
            'name' => 'Worker Server',
            'description' => '',
        ],
        'database' => [
            'name' => 'Database Server',
            'description' => '',
        ],
        'cache' => [
            'name' => 'Cache Server',
            'description' => '',
        ],
    ],

    'programs' => [
        'nginx' => 'Nginx',
        'gunicorn' => 'Gunicorn',
        'python' => 'Python',
        'database' => 'Database',
        'cache' => 'Cache',
    ],

    'databases' => [
        'none' => 'None',
        'mysql-8_0' => 'MySQL 8.0',
        'maria-10_5' => 'MariaDB 10.5',
        'postgres-13' => 'PostgreSQL 13',
        'postgres-12' => 'PostgreSQL 12',
    ],

    'caches' => [
        'none' => 'None',
        'redis' => 'Redis',
        'memcached' => 'Memcached',
    ],
];
