<?php

/*
 * Third-party server providers API configuration
 */

use App\Services\DigitalOceanV2;

return [

    // A real one DigitalOcean read-only API token for development and testing
    'do_dev_token' => env('DO_DEV_TOKEN', null),

    'default' => 'digital_ocean_v2',

    'list' => [

        'digital_ocean_v2' => [
            'provider_class' => DigitalOceanV2::class,
            'base_path' => 'https://api.digitalocean.com/v2',
            'auth_type' => 'token',
            'icon' => 'fab fa-digital-ocean',
            'disabled' => false,
            'default_image' => 'ubuntu-20-04-x64',
        ],

        'aws' => [
            'provider_class' => null,
            'base_path' => '',
            'auth_type' => 'basic',
            'icon' => 'fab fa-aws',
            'disabled' => true,
            'default_image' => '',
        ],

        'linode' => [
            'provider_class' => null,
            'base_path' => '',
            'auth_type' => 'token',
            'icon' => 'fab fa-linode',
            'disabled' => true,
            'default_image' => '',
        ],

    ],

];
