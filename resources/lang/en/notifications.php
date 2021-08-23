<?php

use App\Notifications\Deployments\DeploymentFailedNotification;

return [

    'title' => 'Notifications',
    'details-title' => 'Notification details',

    'messages' => [
        DeploymentFailedNotification::class => 'We were unable to deploy project :project.',
    ],

];
