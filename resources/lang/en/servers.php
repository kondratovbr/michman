<?php

use App\Models\Certificate;
use App\Models\Python;
use App\States\Daemons;
use App\States\Certificates;
use App\States\Servers;

return [

    'title' => 'Servers',

    'placeholder' => [
        'preparing' => 'Preparing your server',
        'deleting' => 'Deleting server',
    ],

    'states' => [
        Servers\Creating::class => 'Creating',
        Servers\Preparing::class => 'Preparing',
        Servers\Configuring::class => 'Configuring',
        Servers\Ready::class => 'Ready',
        Servers\Deleting::class => 'Deleting',
    ],

    'index' => [

        'title' => 'Active Servers',

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
        'python-version' => 'Python version',
        'database' => 'Database',
        'db-name' => 'Database name',
        'cache' => 'Cache',
        'add-key-to-vcs' => 'Add server\'s SSH key to source control providers',
        'will-be-installed' => 'The following will be installed on the server:',

        'digital-ocean' => [
            'something-wrong' => 'Something went wrong while calling DigitalOcean API.',
            'error-code' => 'DigitalOcean API error code: :code',
        ],

        'modal' => [
            'title' => 'Server Credentials',
            'explanation-1' => 'Your server is creating. It will take a couple of minutes.',
            // TODO: Add a note about updating these passwords later, when that feature is implemented.
            'explanation-2' => 'We created random passwords for your server, please make sure to save them somewhere.',
            'sudo-password' => 'Server Sudo Password',
            'db-password' => 'Database Root Password',
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
        'supervisor' => 'Supervisor',
        'ufw' => 'Uncomplicated Firewall',
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
        'button' => 'Python',

        'table' => [
            'title' => 'Python Versions',
            'description' => 'Install and update different versions of Python on the server.',
            'version' => 'Version',
            'status' => 'Status',
            'cli' => 'CLI',
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
            Python::STATUS_DELETING => 'Deleting',
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

    'database' => [
        'button' => 'Database',

        'form' => [
            'title' => 'Create Database',
            'name' => 'Name',
            'button' => 'Create',
            'grant-access' => 'Grant Full Access For Users',
        ],

        'table' => [
            'title' => 'Databases',
            'empty' => 'No databases created yet.',
            'name' => 'Name',
        ],
    ],

    'database-users' => [
        'form' => [
            'title' => 'Create Database User',
            'name' => 'Name',
            'password' => 'Password',
            'button' => 'Create',
            'grant-access' => 'Grant full access to databases',
            'new-password' => 'New password',
            'edit-user' => 'Edit Database User (:name)',
        ],

        'table' => [
            'title' => 'Database Users',
            'empty' => 'No database users created yet.',
            'name' => 'Name',
        ],
    ],

    'ssh' => [
        'button' => 'SSH Keys',
    ],

    'ssl' => [
        'button' => 'SSL',

        'lets-encrypt' => [
            'title' => 'Let\'s Encrypt',
            'description' => 'Use a service by Let\'s Encrypt to receive a free auto-renewable SSL certificate and configure HTTPS on this server.',
            'explanation' => 'Let\'s Encrypt provides free SSL certificates that are recognized across all browsers, libraries and systems. This is the best way to add HTTPS support to your projects.',
            'button' => 'Request Certificate',

            'domain' => [
                'title' => 'Domain',
            ],
        ],

        'index' => [
            'title' => 'SSL Certificates',
            'empty' => 'This server doesn\'t have any SSL certificates, HTTPS is disabled.'
        ],

        'type' => 'Type',
        'domain' => 'Domain',

        'types' => [
            Certificate::TYPE_LETS_ENCRYPT => 'Let\'s Encrypt',
        ],

        'states' => [
            Certificates\Installing::class => 'Installing',
            Certificates\Installed::class => 'Active',
            Certificates\Deleting::class => 'Deleting',
        ],
    ],

    'daemons' => [
        'button' => 'Daemons',

        'create' => [
            'title' => 'Create Daemon',
            'description' => 'Configure Supervisor to ensure a process started by your command keep running.',
            'button' => 'Create Daemon',
        ],

        'index' => [
            'title'=> 'Active Daemons',
            'empty' => 'No daemons configured on this server.',
            'start' => 'Start Daemon',
            'stop' => 'Stop Daemon',
            'restart' => 'Restart Daemon',
            'delete' => 'Delete Daemon',
            'update-statuses' => 'Update Statuses',
        ],

        'command' => [
            'label' => 'Command',
            'help' => 'To run a Python script from a virtualenv - start with full path to the Python executable inside that venv.'
        ],

        'username' => [
            'label' => 'User',
        ],

        'directory' => [
            'label' => 'Directory',
            'help' => 'Optional - will be run from the user\'s home directory by default.',
        ],

        'processes' => [
            'label' => 'Processes',
            'help' => 'Supervisor can start and maintain multiple instances of your program.',
        ],

        'start-seconds' => [
            'label' => 'Start seconds',
            'help' => 'The number of seconds the program needs to stay running to consider the start successful.',
        ],

        'states' => [
            Daemons\Starting::class => 'Starting',
            Daemons\Active::class => 'Active',
            Daemons\Stopping::class => 'Stopping',
            Daemons\Stopped::class => 'Stopped',
            Daemons\Restarting::class => 'Restarting',
            Daemons\Failed::class => 'Failed',
            Daemons\Deleting::class => 'Deleting',
        ],

        'log-modal-title' => 'Daemon Log',
        'view-log-button' => 'View Log',
        'failed-to-retrieve-logs' => 'We were unable to retrieve this daemon\'s logs from the server.',
    ],

    'manage' => [
        'button' => 'Manage',

        'delete' => [
            'title' => 'Delete Server',
            'info' => 'The server will be permanently removed from Michman as well as from :provider. This action is permanent.',
            'button' => 'Delete Server',

            'modal' => [
                'title' => 'Delete Server :server',
                'field-label' => 'Please type the name of the server (:server) to confirm its deletion.',
            ],
        ],
    ],

];
