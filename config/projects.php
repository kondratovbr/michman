<?php

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
