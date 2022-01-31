<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Spark Path
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the URI at which the Spark billing
    | portal is available. You are free to change this URI to a value that
    | you prefer. You shall link to this location from your application.
    |
    */

    'path' => 'billing',

    /*
    |--------------------------------------------------------------------------
    | Spark Middleware
    |--------------------------------------------------------------------------
    |
    | These are the middleware that requests to the Spark billing portal must
    | pass through before being accepted. Typically, the default list that
    | is defined below should be suitable for most Laravel applications.
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | These configuration values allow you to customize the branding of the
    | billing portal, including the primary color and the logo that will
    | be displayed within the billing portal. This logo value must be
    | the absolute path to an SVG logo within the local filesystem.
    |
    */

    // TODO: CRITICAL! Don't forget to at least add a logo here.
    // 'brand' =>  [
    //     'logo' => realpath(__DIR__.'/../public/svg/billing-logo.svg'),
    //     'color' => 'bg-gray-800',
    // ],

    /*
    |--------------------------------------------------------------------------
    | Proration Behavior
    |--------------------------------------------------------------------------
    |
    | This value determines if charges are prorated when making adjustments
    | to a plan such as incrementing or decrementing the quantity of the
    | plan. This also determines proration behavior if changing plans.
    |
    */

    'prorates' => true,

    /*
    |--------------------------------------------------------------------------
    | Spark Billables
    |--------------------------------------------------------------------------
    |
    | Below you may define billable entities supported by your Spark driven
    | application. You are free to have multiple billable entities which
    | can each define multiple subscription plans available for users.
    |
    | In addition to defining your billable entity, you may also define its
    | plans and the plan's features, including a short description of it
    | as well as a "bullet point" listing of its distinctive features.
    |
    */

    'billables' => [

        'user' => [
            'model' => User::class,

            'trial_days' => 30,

            'default_interval' => 'monthly',

            'plans' => [
                [
                    'name' => 'Yacht',
                    'short_description' => 'Start your voyage strong.',
                    'monthly_id' => env('SPARK_STANDARD_MONTHLY_PLAN'),
                    'yearly_id' => env('SPARK_STANDARD_YEARLY_PLAN'),
                    'yearly_incentive' => 'Two Month Free',
                    'features' => [
                        'One Server',
                        'Unlimited Projects',
                        'Unlimited Deployments',
                        'Automatic Deployments',
                    ],
                    'archived' => false,
                ],
                [
                    'name' => 'Squadron',
                    'short_description' => 'Expand your reach.',
                    'monthly_id' => env('SPARK_UNLIMITED_MONTHLY_PLAN'),
                    'yearly_id' => env('SPARK_UNLIMITED_YEARLY_PLAN'),
                    'yearly_incentive' => 'Two Month Free',
                    'features' => [
                        'Unlimited Servers',
                        'Unlimited Projects',
                        'Unlimited Deployments',
                        'Automatic Deployments',
                    ],
                    'archived' => false,
                ],
                /*
                [
                    'name' => 'Fleet',
                    'short_description' => 'Divide and Conquer.',
                    'monthly_id' => env('SPARK_BUSINESS_MONTHLY_PLAN'),
                    'yearly_id' => env('SPARK_BUSINESS_YEARLY_PLAN'),
                    'features' => [
                        'Unlimited Server',
                        'Unlimited Projects',
                        'Unlimited Deployments',
                        'Automatic Deployments',
                        '...',
                    ],
                    'archived' => false,
                ],
                */
            ],

        ],

    ],

    // TODO: CRITICAL! Make sure this works. And terms are written.
    // Spark will show a link to the page with Terms and Conditions at this URL.
    'terms_url' => '/terms',

];