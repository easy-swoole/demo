# Worker会不会同时被两客户端访问？
不会。
# 单例模式写法导致数据一直存在是为什么？
因为easyswoole是常驻内存的，static使用的时候要注意时机释放，详细文档请见：[《swoole_server中内存管理机制》](https://wiki.swoole.com/wiki/page/324.html)
# 用了很多第三方类库都存在有$_GET，$_POST等超全局变量，而swoole默认情况下值是空的怎么办？
可以在Event.php里的OnRequest方法里对超全局变量进行赋值。


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

