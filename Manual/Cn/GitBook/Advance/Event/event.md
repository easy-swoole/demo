# 系统事件
easySwoole预留了多种全局事件入口，以方便用户更加自由地使用easySwoole框架。
其中除框架预处理，其余的事件入口均在Conf/Event.php下，其中Event类必须继承use Core\AbstractInterface\AbstractEvent。以下为开发者常用事件：

* [frameInitialize](/Advance/Event/frameInitialize.md)

* [beforeWorkerStart](/Advance/Event/beforeWorkerStart.md)

* [onWorkerStart](/Advance/Event/onWorkerStart.md)

* [onRequest](/Advance/Event/onRequest.md)

* [onResponse](/Advance/Event/onResponse.md)

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
