框架初始化事件
------

```
function frameInitialize();
```

在CLI模式下启动服务，框架首先进行自身的初始化预处理，然后调用本事件，在执行frameInitialize事件时，框架已经完成的预处理工作有：

- 系统常量ROOT的定义
- 注册自动加载与常用名称空间
- 定义错误处理函数

在该回调函数内可以创建一些全局配置。例如：
```
date_default_timezone_set('Asia/Shanghai');
```
或者是引入第三方文件与名称空间：
```
$loader = AutoLoader::getInstance();
$loader->requireFile("App/Vendor/Aip/Speech/AipSpeech.php");
$loader->addNamespace("OSS","App/Vendor/Ali/OSS");
```

>注意：执行该事件时，还未初始化(创建运行)目录，不建议做多余操作。

