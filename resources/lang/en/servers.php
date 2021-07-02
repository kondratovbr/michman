<?php

use App\Models\Python;

return [

    'index' => [

        'title' => 'Servers',

        'table' => [
            'server' => 'Server',
            'ip' => 'Public IP',
            'type' => 'Type',
        ],

    ],

    'create' => [

        'title' => 'Provision New Server',
        'button' => 'Create Server',
        'credentials' => 'API Credentials',
        'name' => 'Name',
        'region' => 'Region',
        'size' => 'Size',
        'type' => 'Type',
        'python-version' => 'Python Version',
        'database' => 'Database',
        'db-name' => 'Database Name',
        'cache' => 'Cache',
        'add-key-to-vcs' => 'Add server\'s SSH key to source control providers',
        'will-be-installed' => 'The following will be installed on the server:',
        'digital-ocean' => [
            'something-wrong' => 'Something went wrong while calling DigitalOcean API.',
            'error-code' => 'DigitalOcean API error code: :code',
        ],

    ],

    'types' => [
        'app' => [
            'name' => 'App Server',
            'description' => 'Application servers include everything you need to deploy your Python / Django application. If you don\'t want to install a database, you may disable its installation below.',
            'badge' => 'App',
        ],
        'web' => [
            'name' => 'Web Server',
            'description' => 'Web servers include the web server software you need to deploy your Python / Django application, but do not include a cache or database. These servers are meant to be networked to dedicated cache or database servers.',
            'badge' => 'Web',
        ],
        'worker' => [
            'name' => 'Worker Server',
            'description' => 'Worker servers install Python, but do not install a web server or database. These servers are meant to serve as dedicated queue worker servers that may not even be networked to your web servers, but still need to networked to your database and cache servers.',
            'badge' => 'Worker',
        ],
        'database' => [
            'name' => 'Database Server',
            'description' => 'Database servers are dedicated virtual machines for running only a database that should be networked to your other servers.',
            'badge' => 'DB',
        ],
        'cache' => [
            'name' => 'Cache Server',
            'description' => 'Cache servers install only cache software and are meant as dedicated caches for your applications. These servers should be networked to the rest of your servers.',
            'badge' => 'Cache',
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

    'projects' => [
        'button' => 'Projects',
    ],

    'pythons' => [
        'button' => 'Pythons',

        'table' => [
            'title' => 'Python Versions',
            'description' => 'Install and update different versions of Python on the server.',
            'version' => 'Version',
            'status' => 'Status',
            'cli' => 'CLI',
            'patch-version' => 'Patch Version',
            'patch-button' => 'Update Patch Version',
            'remove-button' => 'Uninstall Python :version',
        ],

        'versions' => [
            '3_9' => '3.9',
            '3_8' => '3.8',
            '2_7' => '2.7',
        ],

        'statuses' => [
            Python::STATUS_INSTALLED => 'Installed',
            Python::STATUS_INSTALLING => 'Installing',
            Python::STATUS_UPDATING => 'Updating',
        ],
    ],

    'firewall' => [
        'button' => 'Firewall',

        'form' => [
            'title' => 'Open a Port In Firewall',
            'button' => 'Open Port',
            'name' => [
                'title' => 'Name',
            ],
            'port' => [
                'title' => 'Port',
                'help' => 'You may provide a port range using a colon character (1:65535)',
            ],
            'from-ip' => [
                'title' => 'From IP',
                'help' => 'You may provide an IPv4 or IPv6 address and you may also provide a subnet.',
            ],
        ],

        'table' => [
            'title' => 'Open Ports',
            'empty' => 'No firewall rules added yet.',
            'name' => 'Name',
            'port' => 'Port',
            'type' => 'Type',
            'from-ip' => 'From IP',
            'any' => 'Any',
            'allow' => 'Allow',
        ],
    ],
];
