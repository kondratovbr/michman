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
            'title' => 'Two-Factor Authentication',
            'description' => 'Add additional security to your account using two-factor authentication.',
            'enabled' => 'You have enabled two-factor authentication.',
            'disabled' => 'You have not enabled two-factor authentication.',
            'explanation' => 'When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',
            'enable' => 'Enable 2FA',
            'disable' => 'Disable 2FA',
            'regenerate-recovery' => 'Regenerate Recovery Codes',
            'show-recovery' => 'Show Recovery Codes',
            'scan-this' => 'Two-factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.',
            'recovery-explanation' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.',
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
            'explanation' => 'This will cancel your subscription and permanently delete all of your account\'s data. Your servers will be preserved and stay active with no changes. This action cannot be undone.',
            'enter-password' => 'Please enter your password to confirm account deletion.',
            'delete-button' => 'Delete Account',
            'modal-title' => 'Delete Account',
        ],

        'oauth' => [
            'title' => ':provider OAuth',
            'authenticated' => 'You are authenticated via :provider.',
            'explanation-start' => 'You can enable access with an email and password in addition to access via :provider by setting a password using',
            'explanation-end' => 'function.',
            'enable-password-access-button' => 'Enable Password Access',
            'review-and-revoke' => 'You can review permissions and revoke access via',
        ],

    ],

    'ssh' => [

        'button' => 'SSH Keys',

        'create' => [
            'title' => 'Add an SSH Key',
            'description' => 'These keys will be automatically added to every new server you create.',
            'button' => 'Add Key',
        ],

        'index' => [
            'title' => 'Active Keys',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'public-key' => [
            'label' => 'Public key',
        ],

        'fingerprint' => [
            'label' => 'Fingerprint',
        ],

        'empty' => 'You haven\'t added any SSH keys yet.',
        'add-to-servers' => 'Add To All Servers',
        'delete' => 'Delete Key',
        'delete-and-remove' => 'Delete and Remove From All Servers',

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
            'do-only' => 'Right now we support only DigitalOcean, but Linode, Amazon Web Services and others are coming soon!',
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
        'refresh-button' => 'Refresh Token',
        'unlink-button' => 'Unlink',
        'connect-to-button' => 'Connect to :provider',
        'connected' => 'Connected to :username',
        'used-by-project' => 'Used by :project project',
        'used-by-n-projects' => 'Used by :count project|Used by :count projects',

    ],

    'api' => [

        'button' => 'Michman API',

    ],

];
