# Http

## 安装

安装项目时请不要覆盖默认的配置文件以及EasySwooleEvent事件注册文件.

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-http
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload -o
```

## 启动

```bash
php easyswoole server start
```
