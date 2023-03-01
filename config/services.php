<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        /*
         * NOTE: GitHub should have a full URL configured in the GitHub OAuth App settings,
         *       but it may have only a prefix, i.e. something like: "https://michman.dev/oauth/github".
         *       The URL configured here will be requested as a callback URL
         *       by Socialite package when redirecting to GitHub,
         *       it will be automatically completed into a full URL by Socialite.
         *       It should match the prefix configured in GitHub settings,
         *       otherwise GitHub will redirect to the one in its settings with an error description
         *       as a URL parameter.
         */
        'redirect' => '/oauth/github/callback',

        /*
         * Custom properties
         */

        // https://docs.github.com/en/developers/apps/building-oauth-apps/authorizing-oauth-apps#directing-users-to-review-their-access
        'review_access_url' => 'https://github.com/settings/connections/applications/' . env('GITHUB_CLIENT_ID'),
    ],

    'gitlab' => [
        'client_id' => env('GITLAB_CLIENT_ID'),
        'client_secret' => env('GITLAB_CLIENT_SECRET'),
        'redirect' => '/oauth/gitlab/callback',
    ],

    'bitbucket' => [
        'client_id' => env('BITBUCKET_CLIENT_ID'),
        'client_secret' => env('BITBUCKET_CLIENT_SECRET'),
        'redirect' => '/oauth/bitbucket/callback',
    ],

    'mailerlite' => [
        'api_key' => env('MAILERLITE_API_KEY'),
        'users_group_id' => env('MAILERLITE_USERS_GROUP_ID'),
    ],
];
