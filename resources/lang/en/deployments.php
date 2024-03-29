<?php

use App\Models\Deployment;

return [

    'deploy-button' => 'Deploy Now',

    'table' => [
        'title' => 'Recent Deployments',
        'empty' => 'The project hasn\'t been deployed yet.',
        'started' => 'Started',
        'status' => 'Status',
        'commit' => 'Commit',
        'duration' => 'Duration',
    ],

    'statuses' => [
        Deployment::STATUS_PENDING => 'Pending',
        Deployment::STATUS_WORKING => 'In Progress',
        Deployment::STATUS_COMPLETED => 'Completed',
        Deployment::STATUS_FAILED => 'Failed',
    ],

    'view-output' => 'View Log',
    'view-output-from-server' => 'View Log From Server',
    'log-modal-title' => 'Deployment Log From',
    'automatic' => 'Quick Deployment',
    'manual' => 'Manual Deployment',

];
