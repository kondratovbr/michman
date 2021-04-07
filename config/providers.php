<?php

/*
 * Third-party server providers API configuration
 */

return [

    'default' => 'digital_ocean_v2',

    'list' => [

        'digital_ocean_v2' => [
            'base_path' => 'https://api.digitalocean.com/v2',
            'auth_type' => 'token',
            'icon' => 'fab fa-digital-ocean',
            'disabled' => false,
        ],

        'aws' => [
            'base_path' => '',
            'auth_type' => 'key-secret',
            'icon' => 'fab fa-aws',
            'disabled' => true,
        ],

        'linode' => [
            'base_path' => '',
            'auth_type' => 'token',
            'icon' => 'fab fa-linode',
            'disabled' => true,
        ],

    ]

];
