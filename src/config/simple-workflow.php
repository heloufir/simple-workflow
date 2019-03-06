<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | This value represents the security layer of the simple workflow REST
    | resources, every value contains a flag named 'secured' which means if the
    | route of the REST resources is secured or not, if this value is set to
    | TRUE, you need to add an array of middlewares to the field 'middlewares'
    |
    */
    'security' => [
        'actions' => [
            'secured' => false,
            'middlewares' => [ ]
        ],
        'status' => [
            'secured' => false,
            'middlewares' => [ ]
        ],
        'modules' => [
            'secured' => false,
            'middlewares' => [ ]
        ],
        'workflows' => [
            'secured' => false,
            'middlewares' => [ ]
        ]
    ],

];
