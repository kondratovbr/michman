<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [

            // TODO: CRITICAL! Make sure workers actually work all queues. I have a list of queues here at the bottom.

            'driver' => 'database',
            'table' => 'jobs',
            // Default queue to dispatch jobs
            'queue' => 'default',
            // Job timeout - job will be released back onto the queue
            // if doesn't finished after this amount of seconds.
            'retry_after' => 60 * 60, // 1 hour
            // Store jobs in DB after an active transaction is committed,
            // Laravel will handle transaction failures when they happen.
            'after_commit' => true,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 60 * 60, // 1 hour
            'block_for' => null,
            'after_commit' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

    /*
     * Custom config values
     */

    'queues' => [
        'default', // For quick-running jobs that don't interact with external services or servers.
        'providers', // For jobs that interact with third-party APIs of server providers and VCS providers.
        'servers', // For jobs that interact with managed servers over SSH.
    ],

];
