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

];
