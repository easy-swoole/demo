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

## 提示

由于`php`本身就不能序列化闭包,该闭包投递是通过反射该闭包函数,获取`php`代码直接序列化`php`代码,然后直接`eval`代码实现的.
所以投递闭包无法使用外部的对象引用,以及资源句柄,复杂任务请使用任务模板方法.

错误代码：
```php
$image = fopen('test.php', 'a');//使用外部资源句柄序列化数据将不存在
$a=1;//使用外部变量将不存在
\EasySwoole\EasySwoole\Task\TaskManager::async(function ($image,$a) {
    var_dump($image);
    var_dump($a);
    $this->testFunction();//使用外部对象的引用将出错
    return true;
},function () {});
```