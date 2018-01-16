# 配置文件

EasySwoole框架提供了非常灵活自由的全局配置功能，配置文件采用PHP返回数组方式定义，对于一些简单的应用，无需修改任何配置，对于复杂的要求，还可以自行扩展自己独立的配置文件和进行动态配置

## 默认配置文件

框架安装完成后系统默认的全局配置文件是项目根目录下的 **Config.php** 文件，该文件的内容如下

```php
<?php

return [
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
    'LOG_DIR'     => EASYSWOOLE_ROOT . '/Log'
];
```

各项目的配置含义如下

- **MAIN_SERVER**  -  默认Server配置
  - **HOST**  -  默认Server监听的地址
  - **PORT**  -  默认Server监听的端口
  - **SERVER_TYPE**  -  默认Server的类型
  - **SOCK_TYPE**  -  默认Server的Sock类型（ 仅 SERVER_TYPE 配置为 TYPE_SERVER 时有效 ）
  - **RUN_MODEL**  -  默认Server的运行模式
  - **SETTING**  -  Swoole Server的运行配置（ 完整配置可见[Swoole文档](https://wiki.swoole.com/wiki/page/274.html) ）
    - **task_worker_num**  -  运行的 task_worker 进程数量
    - **task_max_request**  -  task_worker 完成该数量的请求后将退出，防止内存溢出
    - **worker_num**  -  运行的 worker 进程数量
    - **max_request**  -  worker 完成该数量的请求后将退出，防止内存溢出
- **DEBUG**  -  是否开启调试模式
- **TEMP_DIR**  -  临时文件存放的目录
- **LOG_DIR**  -  日志文件存放的目录

## 配置操作类

配置操作类为 `EasySwoole\Config` 类，使用非常简单，见下面的代码例子，操作类还提供了 `toArray` 方法获取全部配置，`load` 方法重载全部配置，基于这两个方法，可以自己定制更多的高级操作

```php
<?php

use EasySwoole\Config;

$instance = Config::getInstance();

// 获取配置 按层级用点号分隔
$instance->getConf('MAIN_SERVER.SETTING.task_worker_num');

// 设置配置 按层级用点号分隔
$instance->setConf('DATABASE.host', 'localhost');

// 获取全部配置
$conf = $instance->toArray();

// 用一个数组覆盖当前配置项
$conf['DATABASE'] = [
    'host' => '127.0.0.1',
    'port' => 13306
];
$instance->load($conf);
```

> 需要注意的是 由于进程隔离的原因 在Server启动后，动态新增修改的配置项，只对执行操作的进程生效，如果需要全局共享配置需要自己进行扩展

## 添加用户配置项

每个应用都有自己的配置项，添加自己的配置项非常简单，其中一种方法是直接在配置文件的数组中添加即可，如下面的例子

```php
<?php

return [
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
    // 这里是自己添加的 Database 配置项
    'DATABASE'    => [
        'host'       => '127.0.0.1',
        'port'       => 3306,
        'auto_close' => true
    ]
];
```

