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
        ],
        'web' => [
            'disabled' => false,
            'install' => [
                'nginx',
                'gunicorn',
                'python',
            ],
        ],
        'worker' => [
            'disabled' => true,
            'install' => [
                'python',
            ],
        ],
        'database' => [
            'disabled' => true,
            'install' => [
                'database',
            ],
        ],
        'cache' => [
            'disabled' => true,
            'install' => [
                'cache',
            ],
        ],
    ],

];
