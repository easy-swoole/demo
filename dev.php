<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT'           => 9501,
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE'      => SWOOLE_TCP,
        'RUN_MODEL'      => SWOOLE_PROCESS,
        'SETTING'        => [
            'worker_num'            => 8,
            'max_request'           => 5000,
            'task_worker_num'       => 8,
            'task_max_request'      => 1000,
            'reload_async'          => true,//设置异步重启开关。设置为true时，将启用异步安全重启特性，Worker进程会等待异步事件完成后再退出。
            'task_enable_coroutine' => true//开启后自动在onTask回调中创建协程
        ],
    ],
    'TEMP_DIR'    => null,
    'LOG_DIR'     => null,
    'CONSOLE'     => [
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
    'FAST_CACHE'  => [
        'PROCESS_NUM' => 0,
        'BACKLOG'     => 256,
    ],
];
