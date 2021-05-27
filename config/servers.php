<?php

use App\Jobs\Servers\ConfigureAppServerJob;
use App\Scripts\Root\Mysql8_0\InstallMysql8_0Script;
use App\Support\Str;

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

    'python' => [
        '3_9' => [

        ],
        '2_7' => [

        ],
    ],

    'databases' => [
        'mysql-8_0' => [
            'install_script' => InstallMysql8_0Script::class,
        ],
        'maria-10_5' => [
            'install_script' => null,
        ],
        'postgres-13' => [
            'install_script' => null,
        ],
        'postgres-12' => [
            'install_script' => null,
        ],
    ],

    'caches' => [
        'redis' => [

        ],
        'memcached' => [

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
