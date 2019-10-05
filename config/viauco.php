<?php

return [

    /* -----------------------------------------------------------------
     |  Database
     | -----------------------------------------------------------------
     */

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],

    'prefix' => 'api/client',

    'auth' => [
        'middleware' => ['api', 'languageCode']
    ]
];
