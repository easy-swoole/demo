# 目录结构

**easySwoole** 的目录结构是非常灵活的，基本上可以任意定制，没有太多的约束，但是仍然建议遵循下面的目录结构，方便开发

```
project                   项目部署目录
├─Application             应用目录(可以有多个)
│  ├─HttpController           控制器目录
│  │  └─Index.php         默认控制器
│  └─Model                模型文件目录
├─Log                     日志文件目录
├─Temp                    临时文件目录
├─vendor                  第三方类库目录
├─composer.json           Composer架构
├─composer.lock           Composer锁定
├─Config.php              框架全局配置
├─EasySwooleEvent.php     框架全局事件
├─easyswoole              框架管理脚本
├─easyswoole.install      框架安装锁定文件
```

框架的核心代码包位于 **vendor/easyswoole/easyswoole** 目录，结构如下

```
easyswoole                框架代码包
├─bin                     框架脚本目录
│  └─easyswoole           框架管理脚本
├─src                     框架源码
│  ├─Core                 框架核心代码
│  ├─Resource             框架资源文件
│  ├─Config.php           框架配置处理类
```

> 如果项目还需要使用其他的静态资源文件，建议使用 **Nginx** / **Apache** 作为前端Web服务，将请求转发至 easySwoole 进行处理，并添加一个 `Public` 目录作为Web服务器的根目录

