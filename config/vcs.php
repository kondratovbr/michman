<?php

/*
 * Third-party VCS providers API configuration
 */

use App\Services\GitHubV3;

return [

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
        ],

        'gitlab' => [
            'oauth_scopes' => [],
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'disabled' => true,
        ],

        'bitbucket' => [
            'oauth_scopes' => [],
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'disabled' => true,
        ],

    ],

];
