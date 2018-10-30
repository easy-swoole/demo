# 框架安装

框架使用 `Composer` 作为依赖管理工具，在开始安装框架前，请确保已经按上一章节的要求配置好环境并安装好了`Composer` 工具，在安装过程中，会释放框架的文件到项目目录，请保证项目目录有可写入权限

> 关于 Composer 的安装可以参照 [Composer中国全量镜像](https://pkg.phpcomposer.com/#how-to-install-composer) 的安装教程,另外 Composer中国 已经很久没有更新了，请大家使用梯子或者是其他镜像。

## Composer 安装

按下面的步骤进行手动安装

```bash
composer require easyswoole/easyswoole=3.x-dev
php vendor/bin/easyswoole.php install
```

## 报错处理

在一些环境中，特别是使用集成面板安装的 PHP 环境，会出现以下报错：

```bash
dir=$(d=${0%[/\\]*}; cd "$d" > /dev/null; cd '../easyswoole/easyswoole/bin' && pwd)

# See if we are running in Cygwin by checking for cygpath program
if command -v 'cygpath' >/dev/null 2>&1; then
    # Cygwin paths start with /cygdrive/ which will break windows PHP,
    # so we need to translate the dir path to windows format. However
    # we could be using cygwin PHP which does not require this, so we
    # test if the path to PHP starts with /cygdrive/ rather than /usr/bin
    if [[ $(which php) == /cygdrive/* ]]; then
        dir=$(cygpath -m "$dir");
    fi
fi

dir=$(echo $dir | sed 's/ /\ /g')
"${dir}/easyswoole" "$@"
```

关于该问题，搜索了几回谷歌，都说是composer问题。不信执行以下代码也有同样问题

```bash
> php vendor/bin/php-parser
```

暂时解决方案就是用 `yum` 或者是以手动编译的形式重新安装你的 PHP 环境，或者也可以直接指向easySwoole的脚本，若有解决该报错的方案，请与我联系

```bash
# 直接指向easySwoole的管理脚本
php vendor/easyswoole/easyswoole/bin/easyswoole.php install
```



## 手动安装

按下面的步骤进行手动安装

```bash
composer require easyswoole/easyswoole=3.x-dev
php vendor/bin/easyswoole install
```

中途没有报错的话，执行：
```bash
# 启动框架
php easyswoole start
```
此时可以访问 `http://localhost:9501` 看到框架的欢迎页面，表示框架已经安装成功

> 如果第二步的 install 操作报错 请查看上方的报错处理




## Hello World
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
        "easyswoole/easyswoole": "3.x-dev"
    }
}
```

> 实际上就是注册App的名称空间

最后执行 `composer dumpautoload` 命令更新命名空间，框架已经可以自动加载 **App** 目录下的文件了，此时框架已经安装完毕，可以开始编写业务逻辑

```bash
# 更新命名空间映射
composer dumpautoload
# 启动框架
php easyswoole start
```
启动框架后，访问 `http://localhost:9501`即可看到 Hello World 。

## 关于IDE助手

由于 Swoole 的函数并不是PHP标准函数，IDE无法进行自动补全，为了方便开发，可以执行以下命令引入IDE助手，在IDE下即可自动补全 Swoole 相关的函数

```bash
composer require easyswoole/swoole-ide-helper
```

## 示例项目

框架准备了一个示例项目，内有框架大部分功能的示例代码，直接克隆下方的 GitHub 仓库到本地并安装依赖，即可开始体验

> 仓库地址: [https://github.com/easy-swoole/demo/tree/3.x](https://github.com/easy-swoole/demo/tree/3.x)

## 目录结构

**EasySwoole** 的目录结构是非常灵活的，基本上可以任意定制，没有太多的约束，但是仍然建议遵循下面的目录结构，方便开发

```
project                   项目部署目录
├─App                     应用目录(可以有多个)
│  ├─HttpController       控制器目录
│  │  └─Index.php         默认控制器
│  └─Model                模型文件目录
├─Log                     日志文件目录
├─Temp                    临时文件目录
├─vendor                  第三方类库目录
├─composer.json           Composer架构
├─composer.lock           Composer锁定
├─EasySwooleEvent.php     框架全局事件
├─easyswoole              框架管理脚本
├─easyswoole.install      框架安装锁定文件
├─dev.env                 开发配置文件
├─produce.env             生产配置文件
```

> 如果项目还需要使用其他的静态资源文件，建议使用 **Nginx** / **Apache** 作为前端Web服务，将请求转发至 easySwoole 进行处理，并添加一个 `Public` 目录作为Web服务器的根目录

> 注意!请不要将框架主目录作为web服务器的根目录,否则dev.env,produce.env配置将会是可访问的,也可自行排除该文件

