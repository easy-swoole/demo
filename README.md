# Template

本`demo`采用`smarty`进行模版引擎渲染。

`EasySwoole`把需要渲染的数据，通过协程客户端投递到自定义的同步进程中进行渲染并返回结果。

## 安装

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-template
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload -o
```

## 启动

```
php easyswoole server start 
```

## 访问

渲染：http://127.0.0.1:9501

模版引擎重启：http://127.0.0.1:9501/reload