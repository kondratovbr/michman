<?php

/*
 * TODO: CRITICAL! Don't forget to implement install/configuration/etc. scripts for every other version and application I plan on supporting.
 *       See all the nulls in here?
 */

return [

    'types' => [
        'django' => [
            'default_root' => '/static',
        ],
        'flask' => [
            'default_root' => '/flaskr/static',
        ],
        'static' => [
            'default_root' => '/',
        ],
    ],

    'workers' => [
        'celery' => [
            //
        ],
        'celerybeat' => [
            //
        ],
    ],

];
