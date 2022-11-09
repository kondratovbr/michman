<?php

use App\Jobs\Servers\ConfigureAppServerJob;
use App\Support\Str;

// TODO: VERY IMPORTANT! Implement other server types and enable support for them.

// TODO: IMPORTANT! Need to add RabbitMQ option somewhere. Python Celery queue system recommends it, so people will probably want it.

return [

    /*
     * A list of basic server types supported by this app
     * with their default configuration.
     */
    'types' => [
        'app' => [
            'install' => [
                'nginx',
                'gunicorn',
                'python',
                'database',
                'cache',
                'supervisor',
                'ufw',
            ],
            'add_ssh_key_to_vcs' => true,
            'configuration_job_class' => ConfigureAppServerJob::class,
            'enabled' => true,
            'min_ram' => 1000,
        ],
        'web' => [
            'install' => [
                'nginx',
                'gunicorn',
                'python',
                'supervisor',
                'ufw',
            ],
            'add_ssh_key_to_vcs' => true,
            'configuration_job_class' => null,
            'enabled' => true,
        ],
        'worker' => [
            'install' => [
                'python',
                'supervisor',
                'ufw',
            ],
            'add_ssh_key_to_vcs' => true,
            'configuration_job_class' => null,
            'enabled' => false,
        ],
        'database' => [
            'install' => [
                'database',
                'ufw',
            ],
            'add_ssh_key_to_vcs' => false,
            'configuration_job_class' => null,
            'enabled' => false,
        ],
        'cache' => [
            'install' => [
                'cache',
                'ufw',
            ],
            'add_ssh_key_to_vcs' => false,
            'configuration_job_class' => null,
            'enabled' => false,
        ],
    ],

    'python' => [
        // NOTE: The order of versions here is important - it is the order the versions will be shown in the UI.
        'versions' => [
            // TODO: Fix 3.10 support. Last issue was - Numpy (and maybe others) failing to build on
            //       our Ubuntu setups.
            // '3_10' => [
            //     'scripts_namespace' => 'App\Scripts\Root\Python3_10',
            //     'cli' => 'python3.10',
            // ],
            '3_9' => [
                'scripts_namespace' => 'App\Scripts\Root\Python3_9',
                'cli' => 'python3.9',
            ],
            '3_8' => [
                'scripts_namespace' => 'App\Scripts\Root\Python3_8',
                'cli' => 'python3.8',
            ],
            '2_7' => [
                'scripts_namespace' => 'App\Scripts\Root\Python2_7',
                'cli' => 'python2.7',
            ],
        ],

        'default_version' => '3_10',
    ],

    'databases' => [
        'mysql-8_0' => [
            'scripts_namespace' => 'App\Scripts\Root\Mysql8_0',
            'django_url_prefix' => 'mysql',
            'default_port' => '3306',
            'enabled' => true,
        ],
        // TODO: VERY IMPORTANT! Implement and enable support for other databases.
        'maria-10_5' => [
            'scripts_namespace' => null,
            'django_url_prefix' => 'mysql',
            'default_port' => '3306',
            'enabled' => false,
        ],
        'postgres-13' => [
            'scripts_namespace' => null,
            'django_url_prefix' => 'postgres',
            'default_port' => '5432',
            'enabled' => false,
        ],
        'postgres-12' => [
            'scripts_namespace' => null,
            'django_url_prefix' => 'postgres',
            'default_port' => '5432',
            'enabled' => false,
        ],
    ],

    'caches' => [
        'redis' => [
            'scripts_namespace' => 'App\Scripts\Root\Redis',
            'default_port' => '6379',
            'django_url_prefix' => 'redis',
            'enabled' => true,
        ],
        // TODO: VERY IMPORTANT! Implement and enable support for memcached.
        'memcached' => [
            'scripts_namespace' => null,
            'default_port' => '11211',
            'django_url_prefix' => 'memcache',
            'enabled' => false,
        ],
    ],

    'default_ssh_port' => 22,

    'worker_user' => 'michman',

];
