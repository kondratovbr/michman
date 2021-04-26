<?php

return [

    /*
     * A list of basic server types supported by this app
     * with their default configuration.
     */
    'types' => [
        'app' => [
            'disabled' => false,
            'install' => [
                'nginx',
                'gunicorn',
                'python',
                'database',
                'cache',
            ],
            'add_ssh_keys_to_vcs' => true,
        ],
        'web' => [
            'disabled' => false,
            'install' => [
                'nginx',
                'gunicorn',
                'python',
            ],
            'add_ssh_keys_to_vcs' => true,
        ],
        'worker' => [
            'disabled' => true,
            'install' => [
                'python',
            ],
            'add_ssh_keys_to_vcs' => true,
        ],
        'database' => [
            'disabled' => true,
            'install' => [
                'database',
            ],
            'add_ssh_keys_to_vcs' => false,
        ],
        'cache' => [
            'disabled' => true,
            'install' => [
                'cache',
            ],
            'add_ssh_keys_to_vcs' => false,
        ],
    ],

    'python' => [
        '3_9' => [

        ],
        '2_7' => [

        ],
    ],

    'databases' => [
        'mysql-8_0' => [

        ],
        'maria-10_5' => [

        ],
        'postgres-13' => [

        ],
        'postgres-12' => [

        ],
    ],

    'caches' => [
        'redis' => [

        ],
        'memcached' => [

        ],
    ],

];
