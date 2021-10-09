# EasySwoole-RPC 5.x 版本 demo

## 下载 5.x-rpc-demo

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-rpc-5.x
```

## 安装和运行

## 1.安装单机部署版 5.x-rpc-demo(仅支持单机内部进行 rpc 调用)

```bash
# 切到到单机部署 demo 目录
cd deployment_for_stand_alone
# 安装
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
# 然后一直按回车
composer dump-autoload

# 启动服务
php easyswoole server start
```

> 注意：`rpc` 服务端是在 `EasySwoole` 全局事件 `EasySwooleEvent.php` 中进行注册，具体如何请查看 [`EasySwooleEvent.php`](https://gitee.com/1592328848/easyswoole_demo/blob/5.x-rpc/deployment_for_stand_alone/EasySwooleEvent.php)。`rpc` 客户端调用在 [`App\HttpController\RpcClientOne.php`](https://gitee.com/1592328848/easyswoole_demo/blob/5.x-rpc/deployment_for_stand_alone/App/HttpController/RpcClientOne.php)  和 [`App\HttpController\RpcClientTwo.php`](https://gitee.com/1592328848/easyswoole_demo/blob/5.x-rpc/deployment_for_stand_alone/App/HttpController/RpcClientTwo.php) 中进行调用。

## 2.安装分布式部署版本 5.x-rpc-demo

暂时未完成。
