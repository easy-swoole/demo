# 数据跨进程共享
由于进程数据隔离，A进程的数据无法直接的被B使用，为解决该问题，可以尝试使用Swoole自带的[Memory](https://wiki.swoole.com/wiki/page/245.html)模块。
或者也可以尝试使用EasySwoole提供的ShareMemory，或者借助第三方的类似Redis之类的服务。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>