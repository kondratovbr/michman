<?php

use App\Services\Webhooks\GitHubWebhookService;

return [

    'payload_url' => env('WEBHOOKS_URL'),

    // TODO: CRITICAL! Don't forget to implement webhooks for Gitlab and Bitbucket as well.
    'providers' => [
        'github' => [
            'vcs_provider' => 'github_v3',
            'service_class' => GitHubWebhookService::class,
        ],
        'gitlab' => [
            'vcs_provider' => 'gitlab',
            'service_class' => null,
        ],
        'bitbucket' => [
            'vcs_provider' => 'bitbucket',
            'service_class' => null,
        ],
    ],

];
