<?php

use App\States\Webhooks;
use App\States\Workers;

return [

    'types' => [
        'django' => 'Django',
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
        'ssl-enabled' => 'SSL enabled',
        'ssl-disabled' => 'SSL not configured',
    ],

    'create' => [
        'title' => 'Create New Project',
        'no-subscription' => 'You need an active subscription to create new projects.',

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
                'label' => 'Database username',
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
            'gitlab_v4' => 'GitLab',
            'bitbucket_v2' => 'Bitbucket',
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

        'states' => [
            Workers\Starting::class => 'Starting',
            Workers\Active::class => 'Active',
            Workers\Deleting::class => 'Removing',
            Workers\Failed::class => 'Failed',
        ],

        'log-modal-title' => 'Worker Log',
        'failed-to-retrieve-logs' => 'We were unable to retrieve this worker\'s logs from the server.',
        'view-log-button' => 'View Log',
        'update-statuses' => 'Update Statuses',
    ],

    'manage' => [
        'button' => 'Manage',

        'deploy-key' => [
            'title' => 'Project\'s Deploy Key',
            'info' => 'The servers will attempt to clone the project\'s repo using this SSH key. Make sure to add this key to the repository.',
        ],

        'delete' => [
            'title' => 'Delete Project',
            'info' => 'All project data will be deleted from all your servers and from Michman\'s database. This action is permanent.',
            'button' => 'Delete Project',

            'modal' => [
                'title' => 'Delete Project :project',
                'field-label' => 'Please type the name of the project (:project) to confirm its deletion from all servers.',
            ],
        ],
    ],

    'quick-deploy' => [
        'title' => 'Quick Deploy',
        'info' => 'Michman can set up a webhook on your repository, so when you push to your deployment branch a deployment will be triggered automatically. You\'ll receive an email notification about the deployment process.',
        'enable' => 'Enable Quick Deploy',
        'disable' => 'Disable Quick Deploy',
    ],

    'hooks' => [
        'states' => [
            Webhooks\Enabling::class => 'Enabling',
            Webhooks\Enabled::class => 'Enabled',
            Webhooks\Deleting::class => 'Deleting',
        ],
    ],

    'branch' => [
        'title' => 'Deployment Branch',
        'label' => 'Branch',
        'button' => 'Update Branch',
    ],

    'uninstall' => [
        'title' => 'Uninstall Repository',
        'info' => 'All project files will be removed from the servers, project page will be reverted to a placeholder.',
        'button' => 'Uninstall Repository',
    ],

];
