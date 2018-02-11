# 安装框架

easySwoole 使用 **Composer** 进行安装和作为依赖管理工具，所以需要先安装 Composer ，不会安装的话可以百度一下参照安装，也可以参照手册的 `安装Composer` 章节进行安装，在安装过程中，会释放框架的文件到项目目录，请保证项目目录有可写入权限

## 手动安装框架

依次执行下面的命令进行框架安装和初始化工作并启动框架

```bash
composer require easyswoole/easyswoole=2.x-dev
php vendor/bin/easyswoole install
php easyswoole start
```

中途没有报错的话，框架就安装完成了，此时可以访问 `http://localhost:9501/` 看到框架的欢迎页面，表示框架已经安装成功

## 我的HELLO WORLD

在项目根目录下创建如下的目录结构，这个目录是编写业务逻辑的应用目录，编辑 `Index.php` 文件，添加基础控制器的代码

```
project              项目部署目录
----------------------------------
├─Application        应用目录
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

然后编辑根目录下的 `composer.json` 文件，注册应用的命名空间

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "Application/"
        }
    },
    "require": {
        "easyswoole/easyswoole": "2.x-dev"
    }
}

```

执行 `composer dumpautoload` 命令更新命名空间，框架已经可以自动加载 **Application **目录下的文件了，此时框架已经安装完毕，可以开始编写业务逻辑

## 关于IDE助手

由于 Swoole 的函数并不是PHP标准函数，IDE无法进行自动补全，为了方便开发，可以执行以下命令引入IDE助手，在IDE下即可自动补全 Swoole 相关的函数

```bash
composer require easyswoole/swoole-ide-helper
```

## 关于composer报错
在一些环境中，特别是使用集成面板安装的php环境，会出现以下报错：
```
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
关于该问题，搜索了几回谷歌，都说是composer问题。不信执行以下代码也有同样问题。
```
> php vendor/bin/php-parser
```
暂时解决方案就是用yum或者是以手动编译的形式重新安装你的php环境，若有解决该报错的方案，请与我联系。
或者也可以直接指向easySwoole的脚本
```
php vendor/easyswoole/easyswoole/bin/easyswoole install
```