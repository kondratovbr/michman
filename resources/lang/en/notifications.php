<?php

use App\Notifications\Daemons;
use App\Notifications\Deployments;
use App\Notifications\Projects;
use App\Notifications\Providers;
use App\Notifications\Servers;
use App\Notifications\TestNotification;
use App\Notifications\Workers;

return [

    'title' => 'Notifications',
    'details-title' => 'Notification details',
    'time' => 'Time',
    'message' => 'Message',

    'messages' => [
        TestNotification::class => ':message',

        Deployments\DeploymentFailedNotification::class => 'We were unable to deploy project <strong>:project.</strong>',
        Deployments\DeploymentCompletedNotification::class => 'Project <strong>:project</strong> was successfully deployed.',

        Projects\ProjectInstallationFailedNotification::class => 'We were unable to install your project <strong>:project</strong> on a server.',

        Providers\AddingSshKeyToProviderFailedNotification::class => 'We were unable to add an SSH key to your server provider account.',
        Providers\RequestingNewServerFromProviderFailedNotification::class => 'We were unable to request a new server for you from a server provider.',

        Servers\FailedToAddSshKeyToServerNotification::class => 'We were unable to add an SSH key to server <strong>:server.</strong>',
        Servers\FailedToConfigureServerNotification::class => 'We were unable to complete server configuration for server <strong>:server.</strong>',
        Servers\FailedToCreateNewUserOnServerNotification::class => 'We were unable to create new user on server <strong>:server.</strong>',
        Servers\FailedToPrepareServerNotification::class => 'We were unable to prepare server <strong>:server.</strong>',
        Servers\FailedToUploadServerSshKeyToServerNotification::class => 'We were unable to upload a server SSH key to server <strong>:server.</strong>',
        Servers\ServerIsNotSuitableNotification::class => 'The server <strong>:server</strong> is not suitable for Michman operations.',
        Servers\ServerNotAvailableNotification::class => 'We were unable to connect to server <strong>:server.</strong>',

        Projects\WebhookEnablingFailedNotification::class => 'We were unable to set up a webhook (Quick Deploy) for project <strong>:project.</strong>',
        Projects\WebhookDeletingFailedNotification::class => 'We were unable to delete a webhook (Quick Deploy) for project <strong>:project</strong> from your repository.',

        Daemons\DaemonFailedNotification::class => 'A daemon failed to start on a server <strong>:server.</strong>',

        Workers\WorkerFailedNotification::class => 'A queue worker for a project <strong>:project</strong> failed to start.',

        Servers\FailedToInstallCertificateNotification::class => 'We were unable to install an SSL certificate on your server <strong>:server.</strong>',
    ],

    'mails' => [
        'default' => [
            'subject' => 'Michman Notification',
            'greeting' => 'Oy! Michman reporting.',
            'action' => 'Go to Dashboard',
        ],

        Deployments\DeploymentFailedNotification::class => [
            // TODO: Would be nice to mention the project/server name somewhere in subject.
            'subject' => 'Deployment Failed',
            // TODO: Provide some more info here - git commit hash and link to that commit in a repo;
            //       deployment duration;
            //       direct link to see the log.
            'lines' => [
                'Something went wrong when performing a deployment of your project :project.',
            ],
            'action' => 'View Deployments',
        ],

        Deployments\DeploymentCompletedNotification::class => [
            // TODO: Would be nice to mention the project/server name somewhere in subject.
            'subject' => 'Deployment Completed',
            // TODO: Provide some more info here - git commit hash and link to that commit in a repo;
            //       deployment duration;
            //       direct link to see the log.
            'lines' => [
                'Your project :project was successfully deployed.',
            ],
            'action' => 'View Deployments',
        ],
    ],

    'hello' => 'Ahoy!',
    'whoops' => 'Whoopsies!',
    'cant-click' => 'Can\'t click the button? Copy and paste this URL into your web browser:',
    'over' => 'Over and out.',

];
