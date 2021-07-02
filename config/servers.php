<?php

use App\Jobs\Servers\ConfigureAppServerJob;
use App\Support\Str;

/*
 * TODO: CRITICAL! Don't forget to implement install/configuration/etc. scripts for every other version and application I plan on supporting.
 *       See all the nulls in here?
 */

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
            'add_ssh_key_to_vcs' => true,
            'configuration_job_class' => ConfigureAppServerJob::class,
        ],
        'web' => [
            'disabled' => false,
            'install' => [
                'nginx',
                'gunicorn',
                'python',
            ],
            'add_ssh_key_to_vcs' => true,
            'configuration_job_class' => null,
        ],
        'worker' => [
            'disabled' => true,
            'install' => [
                'python',
            ],
            'add_ssh_key_to_vcs' => true,
            'configuration_job_class' => null,
        ],
        'database' => [
            'disabled' => true,
            'install' => [
                'database',
            ],
            'add_ssh_key_to_vcs' => false,
            'configuration_job_class' => null,
        ],
        'cache' => [
            'disabled' => true,
            'install' => [
                'cache',
            ],
            'add_ssh_key_to_vcs' => false,
            'configuration_job_class' => null,
        ],
    ],

    // NOTE: The order of versions here is important - it is the order the versions will be shown in the UI.
    'python' => [
        '3_9' => [
            'scripts_namespace' => 'App\Scripts\Root\Python3_9',
            'cli' => 'python3.9',
        ],
        '3_8' => [
            'scripts_namespace' => 'App\Scripts\Root\Python3_8',
            'cli' => 'python3.8',
        ],
        '2_7' => [
            'scripts_namespace' => null,
            'cli' => 'python2.7',
        ],
    ],

    'databases' => [
        'mysql-8_0' => [
            'scripts_namespace' => 'App\Scripts\Root\Mysql8_0',
        ],
        'maria-10_5' => [
            'scripts_namespace' => null,
        ],
        'postgres-13' => [
            'scripts_namespace' => null,
        ],
        'postgres-12' => [
            'scripts_namespace' => null,
        ],
    ],

    'caches' => [
        'redis' => [
            'scripts_namespace' => 'App\Scripts\Root\Redis',
        ],
        'memcached' => [
            'scripts_namespace' => null,
        ],
    ],

    'default_ssh_port' => 22,
    'worker_user' => Str::camel(env('APP_NAME', 'worker')),
    'required_apt_packages' => [
        'ufw',
        'git',
        'curl',
        'gnupg',
        'gzip',
        'unattended-upgrades',
        'supervisor',
    ],

];
