# 容器服务
easySwoole实现了简单版的IOC，使用 IOC 容器可以很方便的存储/获取资源，实现解耦。
> 注意：在服务启动后，对IOC容器的获取/注入仅限当前进程有效。不对其他worker进程产生影响。

## 方法列表
### getInstance
```
$di = Di::getInstance();
```

### set
函数原型：set($key, $obj,...$arg)
- key：键名
- obj:要注入内容。支持注入对象名，对象实例,闭包，资源，字符串等各种常见变量。
- $arg:若注入的内容为is_callable，则可以设置该参数以供callable执行时传入。
```
$di->set('db',new DbClass());
$di->set('db',DbClass::class);
```

> Di的set方法为懒惰加载模式，若set一个对象名或者闭包，则该对象不会马上被创建。

### get
```
$db = $db->get('db');
```

### delete
```
$di->delete('db');
```

### clear
清空 IoC 容器的所有内容。

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
