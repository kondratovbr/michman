<?php

use App\Models\Worker;
use App\States\Webhooks\Deleting;
use App\States\Webhooks\Enabled;
use App\States\Webhooks\Enabling;

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
                'label' => 'Domain name',
            ],
            'aliases' => [
                'label' => 'Aliases',
                'help' => 'Comma-delimited list of domain name aliases.',
            ],
            'type' => [
                'label' => 'Type',
            ],
            'root' => [
                'label' => 'Web root directory',
            ],
            'python-version' => [
                'label' => 'Python version',
            ],
            'allow-sub-domains' => [
                'label' => 'Allow wildcard sub-domains',
            ],
            'create-database' => [
                'label' => 'Create database',
            ],
            'db-name' => [
                'label' => 'Database name',
            ],
            'create-db-user' => [
                'label' => 'Create database user',
            ],
            'db-user-name' => [
                'label' => 'Database user name',
            ],
            'db-user-password' => [
                'label' => 'Database user password',
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
                'label' => 'Python package name',
            ],
            'install-dependencies' => [
                'label' => 'Install dependencies',
            ],
            'requirements-file' => [
                'label' => 'Requirements file',
                'help' => 'You may also put a path relative to the project\'s root if the file is inside some directory.',
            ],
            'use-deploy-key' => [
                'label' => 'Use deploy key',
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

    'queue' => [
        'types' => [
            'celery' => 'Celery',
            'celerybeat' => 'Celery Beat',
        ],

        'button' => 'Queue',

        'create' => [
            'title' => 'New Queue Worker',
            'button' => 'Create Worker',
            'type' => [
                'label' => 'Type',
            ],
            'server' => [
                'label' => 'Server',
                'help' => 'Choose a server to configure the new worker on.'
            ],
            'app' => [
                'label' => 'Celery app module',
                'help' => 'Custom Celery module. You can leave this empty if your Celery module is a Celery.py file inside your main app directory.',
            ],
            'processes' => [
                'label' => 'Number of child processes',
                'help' => 'Leave this field empty for a reasonable default - a process for every CPU core on the server.',
                'table' => 'Processes',
            ],
            'queues' => [
                'label' => 'Queues',
                'help' => 'Comma-separated names of queues to run by this worker. If empty - defaults to "Celery".',
            ],
            'stop-seconds' => [
                'label' => 'Stop seconds',
                'help' => 'When this worker needs to be stopped - wait for this number of seconds for any running tasks to complete before force stopping the worker. Increase if you have any long-running tasks.',
            ],
            'max-tasks' => [
                'label' => 'Max tasks per child',
                'help' => 'Leave empty for no limit. A child process will be restarted after handling this many tasks. May be useful to combat memory leaks.',
            ],
            'max-memory' => [
                'label' => 'Max memory per child, MiB',
                'help' => 'Leave empty for no limit. Another way to combat memory leaks - a child process will be restarted if it\'s allocated memory goes over this limit. If a single task causes a child process to exceed this limit, the task will be completed before restarting the process.'
            ],
        ],

        'index' => [
            'title' => 'Active Workers',
            'empty' => 'No queue workers configured for this project.',
        ],

        'statuses' => [
            Worker::STATUS_STARTING => 'Starting',
            Worker::STATUS_ACTIVE => 'Active',
            Worker::STATUS_DELETING => 'Removing',
            Worker::STATUS_FAILED => 'Failed',
        ],
    ],

    'quick-deploy' => [
        'title' => 'Quick Deploy',
        'enable' => 'Enable Quick Deploy',
        'disable' => 'Disable Quick Deploy',
    ],

    'hooks' => [
        'states' => [
            Enabling::class => 'Enabling',
            Enabled::class => 'Enabled',
            Deleting::class => 'Deleting',
        ],
    ],

];
