项目初始化
------
俗话说磨刀不误砍柴工，在开始之前要完成一些准备工作以便移植其他框架的组件，首先我们得把框架给安装好，easySwoole是一个非常易于使用的框架，安装一样很简单，只需要切换到项目根目录

```
//命令行快速安装
bash <(curl https://www.easyswoole.com/installer.sh)
//OR
curl https://www.easyswoole.com/installer.php | php
```

即将陪伴我们整个开发周期的easySwoole已经下载好，建议使用如下的目录结构，也可以根据自己的喜好做调整，手记默认以下面的目录结构作为例子演示集成组件的流程

```
project           应用部署目录
├─App             应用目录(存放应用的业务逻辑)
│  ├─Controller   控制器目录(业务主要逻辑的控制器存放在这里)
│  ├─Model        模型目录(数据模型存放在这里)
│  ├─Vendor       第三方插件目录(移植的插件存放在这里)
│  ├─Router.php   路由配置文件(项目HTTP请求路由)
├─Conf            配置目录(存放配置相关的文件)
│  ├─Config.php   框架配置文件(项目的全部配置)
│  ├─Event.php    框架事件入口(贯穿框架生命周期的各个事件)
-----------------------------------------------------
// 作为API框架使用时不需要视图相关的目录
├─Public          Web入口目录(允许对外访问的文件)
│  ├─Static       静态资源目录(存放静态资源文件)
├─Views           项目的视图文件目录
-----------------------------------------------------
├─Temp            临时文件目录
├─vendor          通过Composer加载的第三方包
├─Core            框架核心代码(I'm EasySwoole)
├─composer.json   Composer配置文件
├─server          服务管理脚本
```

上面有一些目录和文件下载好框架的时候是没有的，不要紧，我们在使用到的时候再逐步创建，现在让我们先感受一下，进入项目根目录执行 `php server start`

```
// ↓↓↓ 激动人心的 Hello World 时刻到了 ↓↓↓

$ php server start

  ______                          _____                              _
 |  ____|                        / ____|                            | |
 | |__      __ _   ___   _   _  | (___   __      __   ___     ___   | |   ___
 |  __|    / _` | / __| | | | |  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \
 | |____  | (_| | \__ \ | |_| |  ____) |  \ V  V /  | (_) | | (_) | | | |  __/
 |______|  \__,_| |___/  \__, | |_____/    \_/\_/    \___/   \___/  |_|  \___|
                          __/ |
                         |___/

listen address       0.0.0.0
listen port          9501
worker num           8
task worker num      8
user             
user group            
daemonize            false 
debug enable         true
debug log error      true
debug display error  true
swoole version       2.0.9
easyswoole version   1.0.10

```

然后在浏览器访问 `http://localhost:9501`即可看到easySwoole的欢迎页面