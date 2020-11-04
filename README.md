# Task


## 安装

安装时遇到提示是否覆盖,无脑选择`N`.

```
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-task
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload -o
```

## 配置

配置文件中`MAIN_SERVER`内加入：
```
'TASK'=>[
            'workerNum'=>2,
            'maxRunningNum'=>128,
            'timeout'=>15
        ]
```

`workerNum`为进程数量,0不开启`task`进程.
`maxRunningNum`同时最大运行任务.
`timeout`超时时间.

本`demo`已经配置.

## 启动

```
php easyswoole server start
```

## 访问

投递匿名函数: http://127.0.0.1:9501/task/index

投递任务模版: http://127.0.0.1:9501/task/template

获取task状态: http://127.0.0.1:9501/task/status
