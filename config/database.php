<?php

use Illuminate\Support\Str;
use Illuminate\Database\DBAL\TimestampType;

/*
 * Main database configuration
 *
 * Moved here to be reused multiple times - see below.
 */
$mainDbConfig = [
    'driver' => 'mysql',

    /*
     * Here's an example on how to use separate connections for read/write operations.
     * Which may be usable in a multi-server setup.
     * Options inside 'read'/'write' arrays override main ones for the corresponding type of operation,
     * they're intended to be different, obviously.
     * Of course, stuff like port, username, password, etc. can be overridden as well.
     */
    'read' => [
        'host' => env('DB_HOST', '127.0.0.1'),
    ],
    'write' => [
        'host' => env('DB_HOST', '127.0.0.1'),
    ],
    /*
     * This option is related to multi-server DB setup.
     * When true - after any 'write' operation was done Laravel will continue to use that connection
     * for all subsequent reads as well till the end of the request cycle.
     */
    'sticky' => true,

    'url' => env('DATABASE_URL'),
    // 'host' => env('DB_HOST', '127.0.0.1'), // Overridden above
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'app'),
    'username' => env('DB_USERNAME', 'app'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_0900_ai_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => 'InnoDB',
    // This application depends on the transaction isolation level, so I've added this just to be sure.
    'isolation_level' => 'REPEATABLE READ',
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
];

// Telescope data will be stored in a separate database.
$telescopeDbConfig = $mainDbConfig;
$telescopeDbConfig['database'] = env('TELESCOPE_DB_DATABASE', 'telescope');
$telescopeDbConfig['username'] = env('TELESCOPE_DB_USERNAME', 'telescope');
$telescopeDbConfig['password'] = env('TELESCOPE_DB_PASSWORD', '');

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        // Main application database.
        'mysql' => $mainDbConfig,

        // Separate connection to store some logs in a database
        // independent of transactions on the main database.
        'db-logs' => $mainDbConfig,

        // Separate database to store telescope data.
        'telescope' => $telescopeDbConfig,

        // Database used during testing.
        'testing' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'testing'),
            'username' => env('DB_USERNAME', 'testing'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_0900_ai_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'app'), '_') . '_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', 'cache'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | DBAL Package Configuration
    |--------------------------------------------------------------------------
    |
    | This package is used to perform more complex database migrations,
    | like dropping and modifying columns.
    |
    */

    'dbal' => [
        'types' => [
            'timestamp' => TimestampType::class,
        ],
    ],

];
