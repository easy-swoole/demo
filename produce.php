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
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'max_request' => 5000,
            'task_worker_num' => 8,
            'task_max_request' => 1000,
            'document_root' => EASYSWOOLE_ROOT.'/Static',
            'enable_static_handler' => true,
            'heartbeat_idle_time' => 300, # 5分钟无操作则掉线
            'heartbeat_check_interval' => 60 # 每隔一分钟检查一次
        ],
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    'CONSOLE' => [
        'ENABLE' => true,
        'LISTEN_ADDRESS' => '127.0.0.1',
        'HOST' => '127.0.0.1',
        'PORT' => 9500,
        'EXPIRE' => '120',
        'AUTH' => null,
        'PUSH_LOG' => true,
    ],
    'FAST_CACHE' => [
        'PROCESS_NUM' => 0,
        'BACKLOG' => 256,
    ],
    'DISPLAY_ERROR' => true,
    
    'SYSTEM' => [
        'WS_SERVER_PATH' => 'ws://127.0.0.1:9501',
        'LAST_MESSAGE_MAX' => 101
    ],
    'REDIS' => [
        'HOST'          => '127.0.0.1',
        'PORT'          => 6379,
        'POOL_MAX_NUM'  => 5
    ],    
    'MYSQL' => [
        'host'          => '127.0.0.1',
        'port'          => '3306',
        'user'          => 'root',
        'timeout'       => '5',
        'charset'       => 'utf8mb4',
        'password'      => '',
        'database'      => 'test',
        'POOL_MAX_NUM'  => '20',
        'POOL_TIME_OUT' => '0.1',
    ],
];
