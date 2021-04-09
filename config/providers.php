<?php

/*
 * Third-party server providers API configuration
 */

use App\Services\DigitalOceanV2;

return [

    'default' => 'digital_ocean_v2',

    'list' => [

        'digital_ocean_v2' => [
            'provider_class' => DigitalOceanV2::class,
            'base_path' => 'https://api.digitalocean.com/v2',
            'auth_type' => 'token',
            'icon' => 'fab fa-digital-ocean',
            'disabled' => false,
        ],

        'aws' => [
            'provider_class' => null,
            'base_path' => '',
            'auth_type' => 'basic',
            'icon' => 'fab fa-aws',
            'disabled' => true,
        ],

        'linode' => [
            'provider_class' => null,
            'base_path' => '',
            'auth_type' => 'token',
            'icon' => 'fab fa-linode',
            'disabled' => true,
        ],

    ]

];
