<?php

use App\Notifications\Deployments\DeploymentFailedNotification;
use App\Notifications\Projects\ProjectInstallationFailedNotification;
use App\Notifications\Providers\AddingSshKeyToProviderFailedNotification;
use App\Notifications\Providers\RequestingNewServerFromProviderFailedNotification;
use App\Notifications\Servers\FailedToAddSshKeyToServerNotification;
use App\Notifications\Servers\FailedToConfigureServerNotification;
use App\Notifications\Servers\FailedToPrepareServerNotification;
use App\Notifications\Servers\FailedToUploadServerSshKeyToServerNotification;
use App\Notifications\Servers\ServerIsNotSuitableNotification;
use App\Notifications\Servers\ServerNotAvailableNotification;

return [

    'title' => 'Notifications',
    'details-title' => 'Notification details',

    'messages' => [
        DeploymentFailedNotification::class => 'We were unable to deploy project :project.',

        ProjectInstallationFailedNotification::class => 'We were unable to install your project :project to a server.',

        AddingSshKeyToProviderFailedNotification::class => 'We were unable to add an SSH key to your server provider account.',
        RequestingNewServerFromProviderFailedNotification::class => 'We were unable to request a new server for you from a server provider.',

        FailedToAddSshKeyToServerNotification::class => 'We were unable to add an SSH key to server :server.',
        FailedToConfigureServerNotification::class => 'We were unable to complete server configuration for server :server.',
        FailedToPrepareServerNotification::class => 'We were unable to prepare server :server.',
        FailedToUploadServerSshKeyToServerNotification::class => 'We were unable to upload a server SSH key to server :server.',
        ServerIsNotSuitableNotification::class => 'The server :server is not suitable for Michman operations.',
        ServerNotAvailableNotification::class => 'We were unable to connect to server :server.',
    ],

];
