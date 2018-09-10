# 框架安装

框架使用 `Composer` 作为依赖管理工具，在开始安装框架前，请确保已经按上一章节的要求配置好环境并安装好了`Composer` 工具，在安装过程中，会释放框架的文件到项目目录，请保证项目目录有可写入权限

> 关于 Composer 的安装可以参照 [Composer中国全量镜像](https://pkg.phpcomposer.com/#how-to-install-composer) 的安装教程

## Composer 安装

按下面的步骤进行手动安装

```bash
composer require easyswoole/easyswoole=3.x-dev
php vendor/bin/easyswoole.php install
```

> 如果第二步的 install 操作报错 请查看上方的报错处理

在项目根目录下创建如下的目录结构，这个目录是编写业务逻辑的应用目录，编辑 `Index.php` 文件，添加基础控制器的代码

```
project              项目部署目录
----------------------------------
├─App        应用目录
│  └─HttpController      应用的控制器目录
│     └─Index.php    默认控制器文件
----------------------------------
```

```php
<?php
namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
}
```
然后编辑根目录下的 composer.json 文件，注册应用的命名空间

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "App/"
        }
    },
    "require": {
        "easyswoole/easyswoole": "2.x-dev"
    }
}
```

最后执行 `composer dumpautoload` 命令更新命名空间，框架已经可以自动加载 **App** 目录下的文件了，此时框架已经安装完毕，可以开始编写业务逻辑

```bash
# 更新命名空间映射
composer dumpautoload
# 启动框架
php easyswoole start
```

中途没有报错的话，框架就安装完成了，此时可以访问 `http://localhost:9501` 看到框架的欢迎页面，表示框架已经安装成功


## 关于IDE助手

由于 Swoole 的函数并不是PHP标准函数，IDE无法进行自动补全，为了方便开发，可以执行以下命令引入IDE助手，在IDE下即可自动补全 Swoole 相关的函数

```bash
composer require easyswoole/swoole-ide-helper
```

## 示例项目

框架准备了一个示例项目，内有框架大部分功能的示例代码，直接克隆下方的 GitHub 仓库到本地并安装依赖，即可开始体验

> 仓库地址: [https://github.com/easy-swoole/demo/tree/3.x](https://github.com/easy-swoole/demo/tree/3.x)

