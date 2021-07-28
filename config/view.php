<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Custom Configuration Options
    |--------------------------------------------------------------------------
    |
    | Following are all the custom view-related config options,
    | added specifically for this application.
    |
    */

    'page_title_separator' => ' | ',

    // Directories that should be checked for the Blade templates of server config files.
    'config-views-paths' => [
        base_path('servers'),
    ],

    /*
     * File extensions to Blade rendered mapping. Available renderers:
     *
     * 'blade' - normal Blade templates
     * 'file' - text files to use "as-is", no templating applied
     */
    'config-views-extensions' => [
        'blade' => 'blade',
        'conf.blade' => 'blade',
        'txt.blade' => 'blade',
        'blade.sh' => 'blade',
        'blade.py' => 'blade',
        'conf' => 'file',
        'txt' => 'file',
        'sh' => 'file',
        'py' => 'file',
    ],
];
