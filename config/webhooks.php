<?php

use App\Jobs\Webhooks\HandlePingWebhookJob;
use App\Jobs\Webhooks\HandlePushWebhookJob;
use App\Services\Webhooks\GitHubWebhookService;

return [

    'payload_url' => env('WEBHOOKS_URL'),

    // TODO: CRITICAL! Don't forget to implement webhooks for Gitlab and Bitbucket as well.
    'providers' => [
        'github' => [
            'vcs_provider' => 'github_v3',
            'service_class' => GitHubWebhookService::class,
            'events' => [
                'ping',
                'push',
            ],
        ],
        'gitlab' => [
            'vcs_provider' => 'gitlab_v4',
            'service_class' => null,
            'events' => [
                'ping',
                'push',
            ],
        ],
        'bitbucket' => [
            'vcs_provider' => 'bitbucket',
            'service_class' => null,
            'events' => [
                'ping',
                'push',
            ],
        ],
    ],

    'jobs' => [
        'ping' => HandlePingWebhookJob::class,
        'push' => HandlePushWebhookJob::class,
    ],

];
