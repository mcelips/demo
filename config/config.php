<?php

return [
    // настройки приложения
    'app' => [
        'name'       => 'Demo App',
        'url'        => get_url(''),
        'url.host'   => get_host(),
        'url_prefix' => '',
        'debug'      => true,
        'local_env'  => true,  // environment: true - local, false - prod
        'test_env'   => false, // environment: true - test, false - prod
    ],

    'lang' => [
        'default' => 'ru',
        'allowed' => ['en', 'ru'],
    ],


    // ————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    //      БАЗА ДАННЫХ
    // ————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    'database' => [
        'default' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => '',
            'username'  => '',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
        ],
    ],


    // ————————————————————————————————————————————————————————————————————————————————————————————————————————————————
    //      ПОЧТА
    // ————————————————————————————————————————————————————————————————————————————————————————————————————————————————

    'mail' => [
        'smtp' => [
            'transport'  => 'smtp',
            'host'       => '',
            'username'   => '',
            'password'   => '',
            'port'       => 587,
            'encryption' => false,
            'autoTLS'    => false,
            'debug'      => false,
        ],
        'from' => [
            'address' => '',
            'name'    => '',
        ],
    ],
];
