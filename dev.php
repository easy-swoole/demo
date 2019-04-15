<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME'   => "EasySwoole",
    'MAIN_SERVER'   => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT'           => 9501,
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE'      => SWOOLE_TCP,
        'RUN_MODEL'      => SWOOLE_PROCESS,
        'SETTING'        => [
            'worker_num'       => 8,
            'max_request'      => 5000,
            'task_worker_num'  => 8,
            'task_max_request' => 1000,
        ],
    ],
    'TEMP_DIR'      => null,
    'LOG_DIR'       => null,
    'CONSOLE'       => [
        'ENABLE'         => true,
        'LISTEN_ADDRESS' => '127.0.0.1',
        'HOST'           => '127.0.0.1',
        'PORT'           => 9500,
        'EXPIRE'         => '120',
        'PUSH_LOG'       => true,
        'AUTH'           => [
            [
                'USER'     => 'root',
                'PASSWORD' => '123456',
                'MODULES'  => [
                    'auth', 'server', 'help'
                ],
                'PUSH_LOG' => true,
            ]
        ]
    ],
    'FAST_CACHE'    => [
        'PROCESS_NUM' => 0,
        'BACKLOG'     => 256,
    ],
    'DISPLAY_ERROR' => true,

    /*################ MYSQL CONFIG ##################*/
    'MYSQL'         => [
        'host'          => '127.0.0.1',//防止报错,就不切换数据库了
        'port'          => '3306',
        'user'          => 'root',
        'timeout'       => '5',
        'charset'       => 'utf8mb4',
        'password'      => '123456',
        'database'      => 'test',
        'POOL_MAX_NUM'  => '10',
        'POOL_TIME_OUT' => '0.1'
    ],
    /*################ MYSQL CONFIG 多数据库情况 ##################*/
    'MYSQL2'        => [
        'host'          => '127.0.0.1',//防止报错,就不切换数据库了
        'port'          => '3306',
        'user'          => 'root',
        'timeout'       => '5',
        'charset'       => 'utf8mb4',
        'password'      => '123456',
        'database'      => 'test',//防止报错,就不切换数据库了
        'POOL_MAX_NUM'  => '10',
        'POOL_TIME_OUT' => '0.1'
    ],
    /*################ MYSQL CONFIG 匿名连接池情况 ##################*/
    'MYSQL3'        => [
        'host'          => '127.0.0.1',//防止报错,就不切换数据库了
        'port'          => '3306',
        'user'          => 'root',
        'timeout'       => '5',
        'charset'       => 'utf8mb4',
        'password'      => '123456',
        'database'      => 'test',//防止报错,就不切换数据库了
        'POOL_MAX_NUM'  => '10',
        'POOL_TIME_OUT' => '0.1'
    ],


    /*################ REDIS CONFIG ##################*/
    'REDIS'         => [
        'host'          => '127.0.0.1',
        'port'          => '6379',
        'auth'          => 'test',
        'POOL_MAX_NUM'  => '5',
        'POOL_TIME_OUT' => '0.1',
    ],
];
