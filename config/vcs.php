<?php

/*
 * Third-party VCS providers API configuration
 */

use App\Services\GitHubV3;
use App\Services\GitLabV4;

return [

    // A real one GitHub API token for development
    'github_dev_token' => env('GITHUB_DEV_TOKEN', null),

    // TODO: Do I even use this?
    'default' => 'github_v3',
    
    'list' => [

        // TODO: CRITICAL! Don't forget to implement support for all of these.

        'github_v3' => [
            // GitHub scopes are per-request and should be properly configured here.
            'oauth_scopes' => ['user', 'repo', 'admin:public_key'],
            'provider_class' => GitHubV3::class,
            'base_path' => 'https://api.github.com',
            'auth_type' => 'token',
            'disabled' => false,
            'icon' => 'fab fa-github',
            // TODO: CRITICAL! I should have a scheduled command that will check that this is the current key and notify me on the emergency channel if it isn't.
            'ssh_host_key' => 'github.com ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==',
        ],

        'gitlab_v4' => [
            // GitLab scopes are configured on the OAuth application on gitlab.com,
            // but fewer scopes may be requested by a request.
            'oauth_scopes' => ['read_user', 'api'],
            'provider_class' => GitLabV4::class,
            'base_path' => 'https://gitlab.com/api/v4',
            'auth_type' => 'token',
            'disabled' => false,
            'icon' => 'fab fa-gitlab',
            // TODO: CRITICAL! Don't forget to put one here. Same for Bitbucket.
            'ssh_host_key' => 'gitlab.com ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBFSMqzJeV9rUzU4kWitGjeR4PWSa29SPqJ1fVkhtj3Hw9xjLVXVYrU9QlYWrOLXBpQ6KWjbjTDTdDkoohFzgbEY=',
        ],

        'bitbucket' => [
            // Bitbucket doesn't support granular scopes.
            // Scopes are configured on the OAuth application (consumer) on bitbucket.com
            'oauth_scopes' => [],
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'disabled' => true,
            'icon' => 'fab fa-bitbucket',
            'ssh_host_key' => null,
        ],

    ],

];
