# 文件处理
>Easyswoole 在核心中提供了文件处理的系统组件，文件处理的主要代码实现文件为Core\Http\Message\UploadFile.php;

>而文件处理的核心关键点在于onRequest事件进行全局拦截，通过请求获文件Stream。
Stream 在Core\Component\IO\Stream.php

## 使用

###一，获取基本实例
举🌰上传文件
在控制器中获取获取上传文件的3种方式

```php
    $one   = $this->request()->getUploadedFiles();      // 1获取所有
    $two   = $this->request()->getUploadedFile('file'); // 2获取指定键的
    $three = $this->request()->getSwooleRequest();      // 3获取请求
    $data  = $three->（上传文件时的键名）;
```
####注意：单文件上传与多文件上传获取数据时的区别与遍历

###二，基本实例的操作（uploadFile的使用）
依照上面的🌰：
```php
    $two->getStream();             // 获取文件Stream
    $two->moveTo('Public/Ez.gif'); // 移动文件（file_put_contents实行）
    $two->getSize();               // 获取文件大小
    $two->getErroe();              // 获取错误序号
    $two->getClientFilename();     // 获取客户端文件名
    $two->getClientMediaType();    // 获取客户端媒体类型

```
> 基于以上可以自己构建或扩展更加丰富的相关文件处理。




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
