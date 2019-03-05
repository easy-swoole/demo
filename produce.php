<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [// 默认Server配置
        'LISTEN_ADDRESS' => '0.0.0.0',// 默认Server监听的地址**(3.0.7以前 为 HOST)
        'PORT'           => 9501,//默认Server监听的端口
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SERVER, // 可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE'      => SWOOLE_TCP,//该配置项当为SERVER_TYPE值为TYPE_SERVER时有效
        'RUN_MODEL'      => SWOOLE_PROCESS,// 默认Server的运行模式
        'SETTING'        => [// Swoole Server的运行配置（ 完整配置可见[Swoole文档](https://wiki.swoole.com/wiki/page/274.html) ）
            'worker_num'               => 8,//运行的 task_worker 进程数量
            'max_request'              => 5000,// task_worker 完成该数量的请求后将退出，防止内存溢出
            'task_worker_num'          => 8,//运行的 worker 进程数量
            'task_max_request'         => 1000,// worker 完成该数量的请求后将退出，防止内存溢出
            'enable_static_handler'    => true,//加入以下两条配置以返回静态文件
            'document_root'            => EASYSWOOLE_ROOT . "/Static",
        ],
    ],
    'TEMP_DIR'    => null,//临时文件存放的目录
    'LOG_DIR'     => null,//日志文件存放的目录
];