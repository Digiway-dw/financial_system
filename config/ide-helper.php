<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filename & Format
    |--------------------------------------------------------------------------
    |
    | The default filename for the helper file.
    */

    'filename' => '_ide_helper.php',

    /*
    |--------------------------------------------------------------------------
    | Fluent methods
    |--------------------------------------------------------------------------
    |
    | Set to true to generate helper methods for fluent methods
    */

    'include_fluent' => false,

    /*
    |--------------------------------------------------------------------------
    | Write Model magic methods
    |--------------------------------------------------------------------------
    |
    | Set to false to disable writing magic methods to model files
    */

    'write_model_magic_where' => false,

    /*
    |--------------------------------------------------------------------------
    | Write Eloquent model mixins
    |--------------------------------------------------------------------------
    |
    | Set to false to disable writing model mixins
    */

    'write_eloquent_model_mixins' => false,

    /*
    |--------------------------------------------------------------------------
    | Include helpers
    |--------------------------------------------------------------------------
    |
    | Set to true to include helper files in the generated helper file
    */

    'include_helpers' => false,

    /*
    |--------------------------------------------------------------------------
    | Helper files to include
    |--------------------------------------------------------------------------
    */

    'helper_files' => [
        // Add helper files that should be included
    ],

    /*
    |--------------------------------------------------------------------------
    | Model locations to include
    |--------------------------------------------------------------------------
    */

    'model_locations' => [
        'app/Models',
        'app/Domain/Entities',
    ],

    /*
    |--------------------------------------------------------------------------
    | Extra classes to include
    |--------------------------------------------------------------------------
    */

    'extra' => [
        'Eloquent' => ['Illuminate\Database\Eloquent\Builder', 'Illuminate\Database\Query\Builder'],
        'Session' => ['Illuminate\Session\Store'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Magic where methods
    |--------------------------------------------------------------------------
    */

    'magic' => [
        'Log' => [
            'debug'     => 'Monolog\Logger::addDebug',
            'info'      => 'Monolog\Logger::addInfo',
            'notice'    => 'Monolog\Logger::addNotice',
            'warning'   => 'Monolog\Logger::addWarning',
            'error'     => 'Monolog\Logger::addError',
            'critical'  => 'Monolog\Logger::addCritical',
            'alert'     => 'Monolog\Logger::addAlert',
            'emergency' => 'Monolog\Logger::addEmergency',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Interfaces that should be ignored
    |--------------------------------------------------------------------------
    */

    'ignored_models' => [
        // Models to ignore when generating helper
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom DB types
    |--------------------------------------------------------------------------
    */

    'custom_db_types' => [
        // Custom database types
    ],

    /*
    |--------------------------------------------------------------------------
    | Models hooks
    |--------------------------------------------------------------------------
    */

    'model_hooks' => [
        // Custom model hooks
    ],

    /*
    |--------------------------------------------------------------------------
    | Post model hooks
    |--------------------------------------------------------------------------
    */

    'post_model_hooks' => [
        // Custom post model hooks
    ],
];
