<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vine Platform Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for configuring various aspects of your Vine implementation.
    |
    */

    'throttle' => [

        /**
         * The maximum number of validation requests that may be
         * performed per minute by a given API token or user.
         */

        'validations' => env('VALIDATION_THROTTLE_MAX_PER_MINUTE', 60),
    ],

];
