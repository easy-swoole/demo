# EASYSWOOLE Process Demo
> 可以注册多个自定义进程，但进程名字建议不相同。


## 安装

一路回车即可。

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-process
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload -o
``` 

## 启动

```
php easyswoole server start
```


查看进程运行状态：

```bash
php easyswoole process show -d
```

向进程发送数据：

http://127.0.0.1:9501/process/write?text=xxx