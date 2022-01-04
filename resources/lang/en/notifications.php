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

        Deployments\DeploymentFailedNotification::class => 'We were unable to deploy project :project.',
        Deployments\DeploymentCompletedNotification::class => 'Project :project was successfully deployed.',

        Projects\ProjectInstallationFailedNotification::class => 'We were unable to install your project :project to a server.',

        Providers\AddingSshKeyToProviderFailedNotification::class => 'We were unable to add an SSH key to your server provider account.',
        Providers\RequestingNewServerFromProviderFailedNotification::class => 'We were unable to request a new server for you from a server provider.',

        Servers\FailedToAddSshKeyToServerNotification::class => 'We were unable to add an SSH key to server :server.',
        Servers\FailedToConfigureServerNotification::class => 'We were unable to complete server configuration for server :server.',
        Servers\FailedToCreateNewUserOnServerNotification::class => 'We were unable to create new user on server :server.',
        Servers\FailedToPrepareServerNotification::class => 'We were unable to prepare server :server.',
        Servers\FailedToUploadServerSshKeyToServerNotification::class => 'We were unable to upload a server SSH key to server :server.',
        Servers\ServerIsNotSuitableNotification::class => 'The server :server is not suitable for Michman operations.',
        Servers\ServerNotAvailableNotification::class => 'We were unable to connect to server :server.',

        Projects\WebhookEnablingFailedNotification::class => 'We were unable to set up a webhook (Quick Deploy) for project :project',
        Projects\WebhookDeletingFailedNotification::class => 'We were unable to delete a webhook (Quick Deploy) for project :project from your repository.',

        Daemons\DaemonFailedNotification::class => 'A daemon failed to start on a server :server.',

        Workers\WorkerFailedNotification::class => 'A queue worker for a project :project failed to start.',

        Servers\FailedToInstallCertificateNotification::class => 'We were unable to install an SSL certificate on your server :server',
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
            'lines' => [
                'Something went wrong when performing a deployment of your project :project.',
            ],
            'action' => 'View Deployments',
        ],

        Deployments\DeploymentCompletedNotification::class => [
            // TODO: Would be nice to mention the project/server name somewhere in subject.
            'subject' => 'Deployment Completed',
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
