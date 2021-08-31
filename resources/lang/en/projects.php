<?php

use App\Models\Certificate;

return [

    'types' => [
        'django' => 'Django / General Python',
        'flask' => 'Flask',
        'static' => 'Static HTML',
    ],

    'index' => [
        'title' => 'Active Projects',

        'table' => [
            'domain' => 'Domain',
            'repo' => 'Repository',
            'last-deployed' => 'Last Deployed',
        ],

        'empty' => 'No projects created yet.',
    ],

    'create' => [
        'title' => 'Create New Project',

        'form' => [
            'button' => 'Create Project',

            'domain' => [
                'label' => 'Domain Name',
            ],
            'aliases' => [
                'label' => 'Aliases',
                'help' => 'Comma-delimited list of domain name aliases.',
            ],
            'type' => [
                'label' => 'Type',
            ],
            'root' => [
                'label' => 'Web Root Directory',
            ],
            'python-version' => [
                'label' => 'Python Version',
            ],
            'allow-sub-domains' => [
                'label' => 'Allow Wildcard Sub-Domains',
            ],
            'create-database' => [
                'label' => 'Create Database',
            ],
            'db-name' => [
                'label' => 'Database Name',
            ],
            'create-db-user' => [
                'label' => 'Create Database User',
            ],
            'db-user-name' => [
                'label' => 'Database User Name',
            ],
            'db-user-password' => [
                'label' => 'Database User Password',
            ],
        ],
    ],

    'repo' => [
        'configure' => [
            'title' => 'Configure Repository',
            'button' => 'Install Repository',
            'vcs' => [
                'label' => 'Provider',
            ],
            'repo' => [
                'label' => 'Repository',
            ],
            'branch' => [
                'label' => 'Branch',
            ],
            'package' => [
                'label' => 'Python Package Name',
            ],
            'install-dependencies' => [
                'label' => 'Install Dependencies',
            ],
            'requirements-file' => [
                'label' => 'Requirements File',
                'help' => 'You may also put a path relative to the project\'s root if the file is inside some directory.',
            ],
            'use-deploy-key' => [
                'label' => 'Use Deploy Key',
                'enabled-message' => 'Make sure to add this Deploy Key to your :provider repository settings before proceeding, otherwise the server won\'t be able to clone the repository.',
                'disabled-message' => 'The server\'s SSH key will be added to your :provider account and the server will have read access to all repositories that your GitHub account has access to.',
            ],
        ],
        'providers' => [
            'github_v3' => 'GitHub',
            'gitlab' => 'GitLab',
            'bitbucket' => 'Bitbucket',
        ],
    ],

    'deployment' => [
        'button' => 'Deployment',
    ],

    'config' => [
        'button' => 'Configuration',

        'env' => [
            'title' => 'Site Environment',
        ],
        'deploy-script' => [
            'title' => 'Deploy Script',
            'explanation' => 'This script will be run during the deployment process after the project is pulled from its git repository. Gunicorn will be automatically reloaded after the script is finished.',
        ],
        'nginx-config' => [
            'title' => 'Nginx Config',
        ],
        'gunicorn-config' => [
            'title' => 'Gunicorn Config',
        ],
    ],

    'history' => [
        'button' => 'History',
    ],

    'queue' => [
       'button' => 'Queue',
    ],

    'ssl' => [
        'button' => 'SSL',

        'lets-encrypt' => [
            'title' => 'Let\'s Encrypt',
            'description' => 'Use a free service by Let\'s Encrypt to receive a free auto-renewable SSL certificate and configure HTTPS for this project.',
            'explanation' => 'Let\'s Encrypt provides free SSL certificates that are recognized across all major browsers. This is the best way to add HTTPS support to your project. You may separate multiple domains with commas.',
            'button' => 'Request Certificate',

            'domains' => [
                'title' => 'Domains',
            ],
        ],

        'index' => [
            'title' => 'SSL Certificates',
            'empty' => 'This project doesn\'t have any SSL certificates, HTTPS is disabled.'
        ],

        'type' => 'Type',
        'domains' => 'Domains',

        'types' => [
            Certificate::TYPE_LETS_ENCRYPT => 'Let\'s Encrypt',
        ],
    ],

];
