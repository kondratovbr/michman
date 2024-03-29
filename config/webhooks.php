<?php

use App\Jobs\Webhooks\HandlePingWebhookJob;
use App\Jobs\Webhooks\HandlePushWebhookJob;
use App\Services\Webhooks\BitbucketWebhookService;
use App\Services\Webhooks\GitHubWebhookService;
use App\Services\Webhooks\GitLabWebhookService;

return [

    'payload_url' => env('WEBHOOKS_URL'),

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
            'service_class' => GitLabWebhookService::class,
            'events' => [
                'push',
            ],
        ],
        'bitbucket' => [
            'vcs_provider' => 'bitbucket_v2',
            'service_class' => BitbucketWebhookService::class,
            'events' => [
                'push',
            ],
        ],
    ],

    'jobs' => [
        'ping' => HandlePingWebhookJob::class,
        'push' => HandlePushWebhookJob::class,
    ],

];
