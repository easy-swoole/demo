# 系统事件
事件类似`ThinkPHP`的行为或者钩子，是框架在执行过程中预留的开发者执行一些业务逻辑的入口，easySwoole预留了多种全局事件入口，以方便用户更加自由地使用easySwoole框架

其中除框架预处理，其余的事件入口均在`Conf/Event.php`下，其中`Event`类必须继承`Core\AbstractInterface\AbstractEvent`类

以下为框架提供给开发者处理业务逻辑的入口：

|事件名称|事件入口|
|:---:|:---:|
|`框架初始化`|[frameInitialize](/Advance/Event/frameInitialize.md)|
|`框架初始化完成`|[frameInitialized](/Advance/Event/frameInitialized.md)|
|`主进程启动`|[onStart](/Advance/Event/onStart.md)|
|`主进程退出`|[onShutdown](/Advance/Event/onShutdown.md)|
|`服务启动前`|[beforeWorkerStart](/Advance/Event/beforeWorkerStart.md)|
|`服务启动`|[onWorkerStart](/Advance/Event/onWorkerStart.md)|
|`服务退出`|[onWorkerStop](/Advance/Event/onWorkerStop.md)|
|`服务异常`|[onWorkerError](/Advance/Event/onWorkerError.md)|
|`收到请求`|[onRequest](/Advance/Event/onRequest.md)|
|`请求分发`|[onDispatcher](/Advance/Event/onDispatcher.md)|
|`响应请求`|[onResponse](/Advance/Event/onResponse.md)|
|`执行任务`|[onTask](/Advance/Event/onTask.md)|
|`任务结束`|[onFinish](/Advance/Event/onFinish.md)|
