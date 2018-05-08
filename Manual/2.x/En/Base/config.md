# Config
all of the config item is at Config.php file which locate in your project directory root.

## Default Config Detail
```php
<?php

return [
    'SERVER_NAME' => "EasySwoole",  // 
    'MAIN_SERVER' => [
        'HOST'        => '0.0.0.0',
        'PORT'        => 9501,
        'SERVER_TYPE' => \EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SERVER,
        'SOCK_TYPE'   => SWOOLE_TCP,     
        'RUN_MODEL'   => SWOOLE_PROCESS,
        'SETTING'     => [
            'task_worker_num'  => 8,     
            'task_max_request' => 10,
            'max_request'      => 5000, 
            'worker_num'       => 8
        ],
    ],
    'DEBUG'       => true,
    'TEMP_DIR'    => EASYSWOOLE_ROOT . '/Temp',
    'LOG_DIR'     => EASYSWOOLE_ROOT . '/Log',
    'EASY_CACHE'  => [
        'PROCESS_NUM'     => 1,
        'PERSISTENT_TIME' => 0  
    ],
    'CLUSTER'     => [
        'enable'           => false,
        'token'            => null,
        'broadcastAddress' => ['255.255.255.255:9556'],
        'listenAddress'    => '0.0.0.0',
        'listenPort'       => '9556',
        'broadcastTTL'     => 5,
        'nodeTimeout'      => 10,              
        'nodeName'         => 'easySwoole',    
        'nodeId'           => null,          
    ]
];
```

## Add Your Custom Config Item

you and add you config item with php 'key => value' array

## How To Get Config

```
use EasySwoole\Config;

$instance = Config::getInstance();

$arrat = $instance->getConf('MAIN_SERVER');

$num = $instance->getConf('MAIN_SERVER.SETTING.task_worker_num');

```