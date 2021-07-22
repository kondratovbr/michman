<?php

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
                'help' => 'Public static files will be served from this directory. For Django it is usually:',
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
        ],
    ],

    'repo' => [
        'button' => 'Repository',
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
            'install-dependencies' => [
                'label' => 'Install Dependencies',
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
        ]
    ],

];
