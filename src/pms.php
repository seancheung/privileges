<?php

return [

    'models' => [

        'user' => '\App\User',

        'role' => '\App\Role',

        'permission' => '\App\Permission',
    ],

    'pivots' => [
        
        'user_role' => 'role_user',

        'role_permission' => 'permission_role',
    ],

    'foreign_keys' => [

        'user' => 'user_id',

        'role' => 'role_id',

        'permission' => 'permission_id',
    ]

];