<?php

return [

    /*
    | Blade templates live in the frontend/ folder (../frontend/views).
    */
    'paths' => [
        dirname(base_path()).'/frontend/views',
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
