# 配置文件

所有的配置均在 Conf/Config.php 中以数组的形式表现。配置分为系统配置(sysConf)和用户自定义配置(userConf)两种。

## 系统配置项 
```
array(
    "SERVER"=>array(
                "LISTEN"=>"0.0.0.0",
                "SERVER_NAME"=>"",
                "PORT"=>9501,
                "RUN_MODE"=>SWOOLE_PROCESS,//不建议更改此项
                "SERVER_TYPE"=>\Core\Swoole\Config::SERVER_TYPE_WEB,//
                'SOCKET_TYPE'=>SWOOLE_TCP,//当SERVER_TYPE为SERVER_TYPE_SERVER模式时有效
                "CONFIG"=>array(
                    'task_worker_num' => 8, //异步任务进程
                    "task_max_request"=>10,
                    'max_request'=>5000,//强烈建议设置此配置项
                    'worker_num'=>8
                ),
            ),
    "DEBUG"=>array(
                "LOG"=>1,
                "DISPLAY_ERROR"=>1,
                "ENABLE"=>false,
            ),
    "CONTROLLER_POOL"=>true//web或web socket模式有效
);
```
每个配置项的含义如下：

 - LISTEN, 配置 server 监听的 IP 地址。
 - SERVER_NAME, 为当前 server 配置一个名称。
 - PORT， 配置 Server 监听的端口
 - SERVER_TYPE，可选值为SERVER_TYPE_SERVER、SERVER_TYPE_WEB、SERVER_TYPE_WEB_SOCKET 。
 - CONFIG， 这个配置项为一个数组。为 swoole 扩展定义的配置，包括 worker 进程数、task 进程数等等配置。如果需要对 swoole 定义的配置进行设置，可以修改这个数组。更多 swoole 配置相关的内容见 swoole 文档
 - DEBUG['ENABLE']，是否开启 Debug 模式，当这个值为 false, 那么 DEBUG['LOG'] 和 DEBUG['DISPLAY_ERROR'] 配置无效。
 - CONTROLLER_POOL 开启控制器对象池模式
 
 
## 用户配置项
用户自定义配置可以在 Config/Config.php 中的 userConf 函数中添加。每个配置项以 key=>value 的形式添加。

## 获取配置项
运行中要获取/设置配置项请使用 Config::getInstance()->getConf() 和 Config::getInstance()->setConf()。getConf 和 setConf 都支持 . 操作，例如要获取到 PORT 的值，可以使用 Config::getInstance()->getConf('SERVER.PORT') 来获取。
> 注意在服务启动后，setConf()仅仅对当前进程有效。

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
  