<?php

return [

    'login' => 'Log In',
    'register' => 'Sign Up',
    'login-to' => 'Log in to :app',
    'register-on' => 'Sign up for :app',
    'login-via' => 'Log in via',
    'register-via' => 'Sign up via',
    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'logout' => 'Log out',
    'remember' => 'Remember me',
    'new-to' => 'New to :app?',
    'already-registered' => 'Already signed up?',
    'thanks-for-registration' => 'Thanks for signing up! ',
    'verify-email' => 'Before getting started, could you verify your email address by clicking on the link we just emailed to you?',
    'verification-link-sent' => 'A new verification link has been sent to the email address you provided during signing up.',
    'resend-verification-link-button' => 'Resend verification email',
    'forgot-your-password' => 'Forgot your password?',
    'forgot-password-info' => 'No problem. Type your email and we will send you a link to reset your password.',
    'restore-password' => 'Restore Password',
    'email-password-reset' => 'Email Password Reset Link',
    'oauth-linked-to' => "Linked to",
    'link-button' => 'Link',
    'unlink-button' => 'Unlink',

    'tfa' => [
        'please-confirm' => 'Please confirm access to your account by entering the authentication code provided by your authenticator application.',
        'please-confirm-recovery' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
        'use-recovery-button' => 'Use a recovery code',
        'use-code-button' => 'Use an authentication code',
    ],

    'oauth' => [
        'providers' => [
            'github' => [
                'label' => 'GitHub',
                // TODO: Do I even use this now? Should I?
                'review-page-link' => 'GitHub OAuth Apps Settings',
            ],
            'gitlab' => [
                'label' => 'GitLab',
            ],
            'bitbucket' => [
                'label' => 'Bitbucket',
            ],
        ],
        'unlink-provider-oauth' => 'Unlink :provider OAuth',
        'unlink-provider-button' => 'Unlink :provider',
        'are-you-sure' => 'Are you sure you want to disable OAuth via :provider? You won\'t be able to log in using :provider next time!',
    ],

    'verification-email' => [
        'subject' => 'Verify Email Address',
        'first-line' => 'Please click the button below to verify your email address.',
        'action' => 'Verify Email Address',
        'second-line' => 'If you did not create an account, no further action is required, and sorry for bothering you.',
    ],

];
