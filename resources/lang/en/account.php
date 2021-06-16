<?php

return [

    'profile' => [

        'button' => 'Profile',

        'email' => [
            'title' => 'Email',
            'description' => 'Change your account\'s email address.',
        ],

        'password' => [
            'title' => 'Update Password',
            'description' => 'Ensure your account is using a long, random password to stay secure.',
        ],

        'sessions' => [
            'title' => 'Browser Sessions',
            'description' => 'Manage and log out your active sessions on other browsers and devices.',
            'explanation' => 'If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.',
            'this_device' => 'This device',
            'last_active' => 'Last active',
            'enter_password' => 'Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.',
            'logout' => 'Log out other sessions',
        ],

        'tfa' => [
            'title' => 'Two Factor Authentication',
            'description' => 'Add additional security to your account using two factor authentication.',
            'enabled' => 'You have enabled two factor authentication.',
            'disabled' => 'You have not enabled two factor authentication.',
            'explanation' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',
            'enable' => 'Enable 2FA',
            'disable' => 'Disable 2FA',
            'regenerate-recovery' => 'Regenerate Recovery Codes',
            'show-recovery' => 'Show Recovery Codes',
            'scan-this' => 'Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.',
            'recovery-explanation' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.',
        ],

        'delete' => [

            'sorry' => [
                'title' => 'Sorry',
                'content' => 'Sorry, this feature is currently unavailable.',
                'contact-support' => 'Please, contact support to delete your account.',
                'contact-button' => 'Contact Support',
            ],

            'title' => 'Delete Account',
            'description' => 'Permanently delete your account.',
            'explanation' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please save any data or information that you wish to retain.',
            'are-you-sure' => 'Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
            'delete-button' => 'Delete Account',
            'modal-title' => 'Delete Account',
        ],

        'oauth' => [
            'title' => ':provider OAuth',
            'authenticated' => 'You are authenticated via :provider.',
            'explanation-start' => 'You can enable access with an email and password in addition to access via :provider by setting a password using',
            'explanation-end' => 'function.',
            'enable-password-access-button' => 'Enable Password Access',
        ],

    ],

    'ssh' => [

        'button' => 'SSH Keys',

    ],

    'providers' => [

        'button' => 'Server Providers',

        'provider' => [
            'label' => 'Provider',
        ],

        'token' => [
            'label' => 'API Token (Personal Access Token)',
            'label-pat' => 'API Token (Personal Access Token)',
            'label-token' => 'API Token',
        ],

        'key' => [
            'label' => 'API Key',
        ],

        'secret' => [
            'label' => 'Secret',
        ],

        'name' => [
            'label' => 'Name',
            'help' => 'Optional. To help you distinguish provider keys and accounts in case you have a lot of them.',
        ],

        'create' => [
            'do-only' => 'Right now we support only DigitalOcean, but Linode, Amazon Web Services and others are coming very soon!',
            'title' => 'New provider',
            'description' => 'Add a new server provider API credentials.',
            'button' => 'Add Credentials',
        ],

        'statuses' => [
            'active' => 'Active',
            'ready' => 'Ready',
            'error' => 'Error',
            'pending' => 'Pending',
        ],

        /*
         * Lang strings related to specific providers,
         * keys should be the same as in config/providers.php
         */
        'digital_ocean_v2' => [
            'name' => 'DigitalOcean',
            // TODO: Maybe figure out how to localize descriptions? A bit of manual work, not much. Also, maybe figure out how to style the descriptions a bit different, like, italic for example. Note: I may have used these lines in several places - make sure to look for them.
            'size-name' => ':ramGb GB, :count CPU Core, :disk SSD - $:price/month|:ramGb GB, :count CPU Cores, :disk SSD - $:price/month',
            'size-name-description' => ':ramGb GB, :count CPU Core, :disk SSD, :description - $:price/month|:ramGb GB, :count CPU Cores, :disk SSD, :description - $:price/month',
        ],
        'aws' => [
            'name' => 'AWS',
        ],
        'linode' => [
            'name' => 'Linode',
        ],

        'table' => [
            'title' => 'Server Providers',
            'name' => 'Name',
            'provider' => 'Provider',
        ],
    ],

    'vcs' => [

        'button' => 'Source Control',

    ],

    'api' => [

        'button' => 'Michman API',

    ],

];
