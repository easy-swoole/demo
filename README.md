# Nirvana.chat

Nirvana.chat

## 安装

安装时遇到提示是否覆盖 `EasySwooleEvent.php` 请选择否 (输入 n 回车)

```bash
git clone https://github.com/KeyBuffer/nirvana.chat.git
git checkout 3.x-chat
composer install
php vendor/bin/easyswoole install
php easyswoole start
```

## 配置

修改 `dev.php` 内的配置项

```ini
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
        'SERVER_TYPE' => EASYSWOOLE_WEB_SOCKET_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'max_request' => 5000,
            'task_worker_num' => 8,
            'task_max_request' => 1000,
            'document_root' => EASYSWOOLE_ROOT.'/Static',
            'enable_static_handler' => true,
            'heartbeat_idle_time' => 600, # 10分钟无操作则掉线
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
        'WS_SERVER_PATH' => 'ws://127.0.0.1:9501',  # 你的ws服务地址 如 : ws://127.0.0.1:9501
        'LAST_MESSAGE_MAX' => 10
    ],
    'REDIS' => [
        'HOST' => '127.0.0.1',      # redis服务器地址
        'PORT' => 6379              # redis服务器端口
    ]
];
```

## 启动

```bash
php easyswoole start
```
