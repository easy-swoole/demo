# 框架初始化事件

在执行该事件时，框架已经完成的工作有：
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

>注意：执行该事件时，还未初始化应用目录。若需要修改默认应用目录，用以下方式修改:
```
Di::getInstance()->set(SysConst::APPLICATION_DIR,"your DIR");
```

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>
