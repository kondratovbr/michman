<?php

/*
 * Third-party VCS providers API configuration
 */

use App\Services\GitHubV3;

return [

    // A real one GitHub API token for development
    'github_dev_token' => env('GITHUB_DEV_TOKEN', null),

    // TODO: Do I even use this?
    'default' => 'github_v3',
    
    'list' => [

        // TODO: CRITICAL! Don't forget to implement support for all of these.

        'github_v3' => [
            'oauth_scopes' => ['user', 'repo', 'admin:public_key'],
            'provider_class' => GitHubV3::class,
            'base_path' => 'https://api.github.com',
            'auth_type' => 'token',
            'disabled' => false,
            'icon' => 'fab fa-github',
            'deploy_keys_page_url' => '',
            // TODO: CRITICAL! I should have a scheduled command that will check that this is the current key and notify me on the emergency channel if it isn't.
            'ssh_host_key' => 'github.com ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==',
        ],

        'gitlab' => [
            'oauth_scopes' => [],
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'disabled' => true,
            'icon' => 'fab fa-gitlab',
            'deploy_keys_page_url' => '',
            'ssh_host_key' => null,
        ],

        'bitbucket' => [
            'oauth_scopes' => [],
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'disabled' => true,
            'icon' => 'fab fa-bitbucket',
            'deploy_keys_page_url' => '',
            'ssh_host_key' => null,
        ],

    ],

    // TODO: CRITICAL! Don't forget to implement webhooks for Gitlab and Bitbucket as well.
    'hook_providers' => [
        'github' => 'github_v3',
        'gitlab' => 'gitlab',
        'bitbucket' => 'bitbucket',
    ],

    'hook_url' => env('WEBHOOKS_URL'),

];
