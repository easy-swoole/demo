# 自动加载
easySwoole支持标准的PSR-4自动加载。
## 添加名称空间
```
$loader = AutoLoader::getInstance();
$loader->addNamespace('new name space',"dir path");
```
> 如果不懂如何使用，可以参考Core.php中的registerAutoLoader方法，里面的FastRoute、SuperClosure、PhpParser均为第三方组件。

## 引入单个文件
```
$loader->requireFile('file path');
```

> 当成功引入时，返回true,若引入失败则返回false。该函数实际上是对require_once的封装。

## 使用composer进行包管理
EasySwoole同样支持用户使用composer进行包的统一管理。
- 确保已经安装composer
- 在项目目录下，执行项目初始化
- 项目目录composer初始化成功后，可在项目根目录下看到一个vendor的文件夹
- 修改EasySwoole的frameInitialize事件。加入以下代码：
```
$loader = AutoLoader::getInstance();
$loader->requireFile('vendor/autoload.php');
```

至此，EasySwoole开始支持composer。
### composer 测试
在项目目录下执行：
```
composer require phpdr.net/php-curlmulti
```

修改unitTest.php
```
<?php
    require_once 'Core/Core.php';
    \Core\Core::getInstance()->frameWorkInitialize();
    $test = new \Ares333\CurlMulti\Core();
```

执行 php unitTest.php无致命报错，则说明已开始成功使用composer。

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
