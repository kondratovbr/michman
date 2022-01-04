<?php

/*
 * TODO: CRITICAL! Don't forget to implement install/configuration/etc. scripts for every other version and application I plan on supporting.
 */

return [

    // TODO: IMPORTANT. Add a "General Python" type. Should work pretty much the same as Django. Just for clarity.
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
