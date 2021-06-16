<?php

/*
 * Third-party VCS providers API configuration
 */

use App\Services\GitHubV3;

return [

    'default' => 'github',

    'list' => [

        'github_v3' => [
            'provider_class' => GitHubV3::class,
            'base_path' => 'https://api.github.com',
            'auth_type' => 'token',
            'icon' => 'fab fa-github',
            'disabled' => false,
        ],

        'gitlab' => [
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'icon' => 'fab fa-gitlab',
            'disabled' => true,
        ],

        'bitbucket' => [
            'provider_class' => null,
            'base_path' => null,
            'auth_type' => 'token',
            'icon' => 'fab fa-bitbucket',
            'disabled' => true,
        ],

    ],

];
