Composer是PHP用来管理依赖关系的工具。你可以在自己的项目中声明所依赖的外部工具库，Composer会帮你安装这些依赖的库文件，我们首先来为框架添加Composer支持，让集成组件变得更简单，鉴于有些朋友比较排斥Composer，觉得很麻烦，我们这里还是啰嗦一下怎么安装，熟悉Composer的朋友可以跳过这部分，在后面的教程中，相信大家都会认同PHP是世界上最好的语言，Composer是PHP最好的依赖管理工具

安装Composer
------

安装Composer有两种方法，英文能力比较好的朋友可以直接参考Composer官方文档 [传送门](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx) 或者由 [Composer中国全量镜像](https://pkg.phpcomposer.com/) 提供的 [安装教程](https://pkg.phpcomposer.com/#how-to-install-composer) 都有提供常规安装的方法，在这里就不再赘述，如果使用常规安装方法遇到各种错误的可以尝试下面的方法，首先 [点我](https://getcomposer.org/composer.phar) 下载打包好的Composer.phar

拿到Composer.phar就相当于已经拥有了Composer，不敢相信?搞了这么久还没装好居然这样就可以了?在文件目录下执行一发

```
$ php composer.phar -v

   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 1.5.2 2017-09-11 16:59:25

```

看到上面的提示就是局部安装已经成功了，不过此时执行命令还是要进入`composer.phar`所在的目录中或者把`composer.phar`放在项目目录下才能执行，略显麻烦，可以参照 [Composer中国全量镜像](https://pkg.phpcomposer.com/) 提供的 [安装教程](https://pkg.phpcomposer.com/#how-to-install-composer) 中全局安装部分的教程进行全局安装

初始化Composer
------
首先为项目新建一个`composer.json`文件，也可以执行`composer init`进行交互式创建，如果不熟悉或者还没来得及深入了解，不要紧，放心大胆一路回车即可

```
$ composer init

Welcome to the Composer config generator

This command will guide you through creating your composer.json config.

// 指定当前项目的名称(自行填写)
Package name (<vendor>/<name>) [owner/name]:
// 项目描述
Description []:
// 作者信息
Author [evalor <mipone@foxmail.com>, n to skip]:
// 最低稳定版本
Minimum Stability []:
// 项目类型
Package Type (e.g. library, project, metapackage, composer-plugin) []:
// 项目授权协议
License []:

Define your dependencies.

// 是否定义生产环境依赖包(暂不定义输入no并回车)
Would you like to define your dependencies (require) interactively [yes]?
// 是否定义开发环境依赖包(暂不定义输入no并回车)
Would you like to define your dev dependencies (require-dev) interactively [yes]?
```

项目目录下会生成一个`composer.json`配置文件，接下来我们进行composer的初始化，执行`composer install`

```
$ composer install

Loading composer repositories with package information
Updating dependencies (including require-dev)
Nothing to install or update
Generating autoload files
```

至此Composer的初始化已经完成，项目目录下生成了`vendor`文件夹，并且生成了`vendor/autoload.php`文件

集成Composer
------

初始化完成后，我们再将Composer集成到框架中，这是非常简单的，只需要编辑你的`Conf/Event.php`文件，并在`框架初始化事件`中加入如下代码

```
use Core\AutoLoader;

// 框架初始化
function frameInitialize()
{
	$loader = AutoLoader::getInstance();
	$loader->requireFile('vendor/autoload.php');
}
```

> 注意 AutoLoader 类是 Core\AutoLoader 命名空间下的

干得漂亮！至此框架已支持Composer加载第三方类库，接下来的教程将开始集成第三方组件