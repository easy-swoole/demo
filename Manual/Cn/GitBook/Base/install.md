# 安装与启动
easySwoole 项目依赖于 Swoole 扩展，在使用 easySwoole 之前需要先安装 swoole 扩展。
## 快速安装
命令行快速安装：

> bash <(curl http://www.easyswoole.com/installer.sh)

或是：

> curl http://www.easyswoole.com/installer.php | php

## 手动安装

从 [easyswoole](https://github.com/kiss291323003/easyswoole) 下载源码，下载下来之后目录结构如下:
```
├── src      ----------------框架所在目录
├── ide-helper     ----------IDE代码补全提示
└── .htaccess-apache --------Apache 反向代理规则
```
其中，src 目录中的内容为项目需要的,目录结构如下 :
```
├── App   -------------------应用目录
    |----Controller----------控制器目录
    |----Model---------------模型目录
    |----Vendor--------------第三方插件
├── Conf  -------------------配置与事件配置目录
├── Core  -------------------框架核心目录
├── server  -----------------服务管理脚本
└── unitTest.php   ----------单元测试脚本
```
## Hello World
进入 src 目录，执行
```
php server start 
```
启动 easySwoole。在浏览器输入 ip:9501/ 可以看到欢迎使用语说明安装成功。

## 服务启动
easySwoole 不依赖 Apache/Nginx, 自带 HttpServer 功能，进入项目根目录，执行 php server start 就可以启动 easySwoole。easySwoole 只有三个命令参数 ： start(启动), stop(停止), reload(重载)

在启动 easySwoole 的时候也可以指定一些配置参数。例如通过执行 php server start --help 可以查看所有参数和具体的参数含义。

> 这里注意一点，easySwoole 属于常驻内存的应用，当修改代码之后要重启 easySwoole 代码才能生效。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>   