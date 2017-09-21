# easySwoole

* 前言
    * [项目简介](README.md)
    * [环境要求](QianYan/environment.md)
    * [编程须知](QianYan/instruction.md)
* 基础入门
    * Swoole Http Server 基础
        * [Swoole Http Server](Base/Swoole/server.md)
        * [基础事件回调](Base/Swoole/event.md)
        * [常见问题](Base/Swoole/problem.md)
        * [异步进程](Base/Swoole/task.md)
        * [定时器](Base/Swoole/tick.md)
    * [框架安装与启动](Base/install.md)    
    * [框架配置文件](Base/config.md)
    * URL与控制器
        * [URL规则](Base/Controller/url.md)
        * [基础控制器](Base/Controller/controller.md)
        * [REST](Base/Controller/rest.md)
        * [Request对象](Base/Controller/request.md)
        * [Response对象](Base/Controller/response.md)
        * [PSR-7 Http Message](Base/Controller/psr-7.md)
        * [路由](Base/Controller/router.md)
    * [自动加载](Base/loader.md)    
* 框架进阶
    * [系统事件](Advance/Event/event.md)
        * [frameInitialize](Advance/Event/frameInitialize.md)
        * [beforeWorkerStart](Advance/Event/beforeWorkerStart.md)
        * [onRequest](Advance/Event/onRequest.md)
        * [onResponse](Advance/Event/onResponse.md)
    * 系统组件
        * Spl标准库
            * [SplArray](Advance/Component/Spl/splArray.md)
            * [SplBean](Advance/Component/Spl/splBean.md)
            * [SplString](Advance/Component/Spl/splString.md)
        * [API版本控制](Advance/Component/version.md)
        * [容器服务](Advance/Component/di.md)
        * [日志](Advance/Component/log.md)
        * [系统常量](Advance/Component/const.md)
        * [ShareMemory](Advance/Component/shareMemory.md)
    * 系统工具
        * [CURL](Advance/Utility/curl.md)
        * [validate](Advance/Utility/validate.md)
        * [排序](Advance/Utility/sort.md)
        * [随机](Advance/Utility/random.md)
    * [异步进程](Advance/task.md)
    * [定时器](Advance/time.md)
* 示例代码
    * [控制器](Example/controller.md)
    * [Model与数据库](Example/db.md)
    * [web socket]    
    * TCP
    * UDP
    * [直播](Example/live.md)
* 常见问题
    * 数据库断线
    * 数据跨进程共享
* 企业用例     
* [问题反馈](feedBack.md)  
    
