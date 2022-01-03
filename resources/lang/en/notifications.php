<?php

use App\Notifications\Daemons\DaemonFailedNotification;
use App\Notifications\Deployments\DeploymentCompletedNotification;
use App\Notifications\Deployments\DeploymentFailedNotification;
use App\Notifications\Projects\ProjectInstallationFailedNotification;
use App\Notifications\Projects\WebhookDeletingFailedNotification;
use App\Notifications\Providers\AddingSshKeyToProviderFailedNotification;
use App\Notifications\Providers\RequestingNewServerFromProviderFailedNotification;
use App\Notifications\Servers\FailedToAddSshKeyToServerNotification;
use App\Notifications\Servers\FailedToConfigureServerNotification;
use App\Notifications\Servers\FailedToCreateNewUserOnServerNotification;
use App\Notifications\Servers\FailedToPrepareServerNotification;
use App\Notifications\Servers\FailedToUploadServerSshKeyToServerNotification;
use App\Notifications\Servers\ServerIsNotSuitableNotification;
use App\Notifications\Servers\ServerNotAvailableNotification;
use App\Notifications\Projects\WebhookEnablingFailedNotification;
use App\Notifications\TestNotification;
use App\Notifications\Workers\WorkerFailedNotification;

return [

    'title' => 'Notifications',
    'details-title' => 'Notification details',
    'time' => 'Time',
    'message' => 'Message',

    'messages' => [
        TestNotification::class => ':message',

        DeploymentFailedNotification::class => 'We were unable to deploy project :project.',
        DeploymentCompletedNotification::class => 'Project :project was successfully deployed.',

        ProjectInstallationFailedNotification::class => 'We were unable to install your project :project to a server.',

        AddingSshKeyToProviderFailedNotification::class => 'We were unable to add an SSH key to your server provider account.',
        RequestingNewServerFromProviderFailedNotification::class => 'We were unable to request a new server for you from a server provider.',

        FailedToAddSshKeyToServerNotification::class => 'We were unable to add an SSH key to server :server.',
        FailedToConfigureServerNotification::class => 'We were unable to complete server configuration for server :server.',
        FailedToCreateNewUserOnServerNotification::class => 'We were unable to create new user on server :server.',
        FailedToPrepareServerNotification::class => 'We were unable to prepare server :server.',
        FailedToUploadServerSshKeyToServerNotification::class => 'We were unable to upload a server SSH key to server :server.',
        ServerIsNotSuitableNotification::class => 'The server :server is not suitable for Michman operations.',
        ServerNotAvailableNotification::class => 'We were unable to connect to server :server.',

        WebhookEnablingFailedNotification::class => 'We were unable to set up a webhook (Quick Deploy) for project :project',
        WebhookDeletingFailedNotification::class => 'We were unable to delete a webhook (Quick Deploy) for project :project from your repository.',

        DaemonFailedNotification::class => 'A daemon failed to start on a server :server.',

        WorkerFailedNotification::class => 'A queue worker for a project :project failed to start.',
    ],

    'mails' => [
        'default' => [
            'subject' => 'Michman Notification',
            'greeting' => 'Oy! Michman reporting.',
            'action' => 'Go to Dashboard',
        ],

        DeploymentFailedNotification::class => [
            // TODO: Would be nice to mention the project/server name somewhere in subject.
            'subject' => 'Deployment Failed',
            'lines' => [
                'Something went wrong when performing a deployment of your project :project.',
            ],
            'action' => 'View Deployments',
        ],

        DeploymentCompletedNotification::class => [
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
