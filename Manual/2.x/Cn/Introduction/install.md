# 框架安装

框架使用 `Composer` 作为依赖管理工具，在开始安装框架前，请确保已经按上一章节的要求配置好环境并安装好了`Composer` 工具，在安装过程中，会释放框架的文件到项目目录，请保证项目目录有可写入权限

> 关于 Composer 的安装可以参照 [Composer中国全量镜像](https://pkg.phpcomposer.com/#how-to-install-composer) 的安装教程



## 快速安装

```php
# 创建项目
composer create-project easyswoole/app easyswoole

# 进入项目目录并启动
cd easyswoole
php easyswoole start
```

执行上面的命令，会在执行命令的目录创建 `easyswoole` 文件夹，创建好的项目已经拥有基本的目录结构，可以直接启动项目，访问浏览器 `http://127.0.0.1:9501` 即可访问到控制器

> 如果在命令执行过程中遇到报错 或者无法启动等问题 请继续往下阅读查看解决方案 或尝试手动安装 如果仍不能解决问题，请加入左上角目录上方的在线交流群 寻求帮助



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
php vendor/easyswoole/easyswoole/bin/easyswoole install
```



## 手动安装

按下面的步骤进行手动安装

```bash
composer require easyswoole/easyswoole=2.x-dev
php vendor/bin/easyswoole install
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
use EasySwoole\Core\Http\AbstractInterface\Controller;
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

## 使用Docker镜像

可以使用docker仓库的镜像 `encircles/easyswoole:latest`
> 映射本地9501端口, 可以自行修改
* `docker run -d -p 9501:9501 --name container-name encircles/easyswoole:latest`  




如果docker仓库的镜像不能满足你的开发需求, 可以修改 Dockerfile 来自定义镜像

在 composer.json 添加
```
"repositories": [
    {
        "type": "composer",
        "url": "https://packagist.phpcomposer.com"
    },
    { "packagist": false }
]
```

修改Dockerfile之后, 在当前目录下运行命令
* `docker build -t yourImageName .`

运行容器
* `docker run -p 9501:9501 --name container-name yourImageName`

> 如果运行失败, 可以通过 `docker logs containerName` 查看日志


## 关于IDE助手

由于 Swoole 的函数并不是PHP标准函数，IDE无法进行自动补全，为了方便开发，可以执行以下命令引入IDE助手，在IDE下即可自动补全 Swoole 相关的函数

```bash
composer require easyswoole/swoole-ide-helper
```



## 常见问题

###安装后第一次启动 报错协程 ID 只能为 int 或 null 

请确保 `swoole` 拓展版本大于 1.9.23 或者是大于 2.1.0，并且使用了easyswoole仓库提供的IDE助手

> composer require easyswoole/swoole-ide-helper:dev-master

原因在于其他仓库的IDE助手未及时更新，协程函数默认返回1，实际上应该是-1



### 启动后无法加载首页 一直处于加载中的状态

请检查当前 swoole 拓展版本是否为 `2.1.2` ，该版本存在 BUG ，如果使用了该版本，升级到 `2.1.3` 或更高版本即可解决，可以从以下几个地址的任意一个获取到 `2.1.3` 版本的拓展

1. 官方 GitHub 仓库 : [点我直达](https://github.com/swoole/swoole-src/releases/tag/v2.1.3)
2. 官方 PECL 仓库 : [点我直达](http://pecl.php.net/package/swoole)



## 示例项目

框架准备了一个示例项目，内有框架大部分功能的示例代码，直接克隆下方的 GitHub 仓库到本地并安装依赖，即可开始体验

> 仓库地址: [https://github.com/easy-swoole/demo](https://github.com/easy-swoole/demo)

```bash
# 克隆仓库
git clone https://github.com/easy-swoole/demo.git easyswoole
cd easyswoole

# 初始化依赖并启动
composer install
php vendor/bin/easyswoole install
php easyswoole start
```
