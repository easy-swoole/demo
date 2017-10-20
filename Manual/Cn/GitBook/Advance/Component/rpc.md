# RPC & SOA
EasySwoole 同样可以做串行、并行化的SOA服务调用，底层基于SWOOLE_TCP实现，支持自定义消息加解密，为方便多种客户端（不同语言）调用，服务交互采用json格式传递，开发者可以快速以其他语言实现。

## 服务端
在easySWoole的beforeWorkerStart事件中，去注册SOA服务。实例代码：
```
##use Core\Component\RPC\Common\Package;
##use Core\Component\RPC\Server\Server;
##use Core\Component\Socket\Client\TcpClient;

$conf = new \Core\Component\RPC\Common\Config();
$soa = new Server($conf);
 //注册服务1
$soa->registerServer("server1")->registerAction('action1',function (Package $res,Package $req,TcpClient $client){
            $arg = $req->getArgs();
            var_dump($arg);
            $res->setMessage("this is server1 action1 res");
})->registerAction("action2",function (){
            return "this is server1 action1 res2";
});
//注册服务2
$subServer = $soa->registerServer("server2");
$subServer->registerAction('action1',function (){
    usleep(1000000);
    return 'this is server2 action1 res1';
});
$subServer->setDefaultAction(function (){
    return "this is default";
});
//该服务监听9502端口
$soa->attach(9502);
```
一个服务，可以存在多个action，每个action回调参数有三个分别是响应的Package,请求的Package，还有客户端信息。

## 客户端
```
use  \Core\Component\RPC\Common\Config;
use \Core\Component\RPC\Client\Client;
use Core\Component\RPC\Common\Package;
//设置服务信息
$conf = new Config();
$conf->setPort('9502');
$conf->setHost('127.0.0.1');
$client = new Client();

$client->selectServer($conf)->addCall('server1','action1',array(1,2,3),function (Package $res){
    var_dump($res->__toString());
},function (Package $req){
    echo "error";
});
//这里可以再新建一个服务器配置项，实现调用不同主机
$sub = $client->selectServer($conf);
$sub->addCall('server2','action1',array(),function (){
    var_dump("server2 action success");
},function (){
    var_dump("server2 action fail");
});
//存在多个任务时，自动并行调用，可以设置超时时间，单位为毫秒
$client->run(500);
```
调用一个服务action的时候，需要指定server名称，action名称。可选参数为：传递参数，成功回调，失败回调。
成功回调参数为服务端返回的Package，失败回调参数为原始请求的Package。

## Package对象
Package对象其实是一个Bean,其成员变量为：
```
protected $serverName;//服务名
protected $action;//action名
protected $args;//参数
protected $message;//信息（执行结果）
protected $errorCode;//错误号
```
以上成员变量均有对于的get、set方法。

## 消息加密
### 实现消息加解密的类
解密示例：
```
namespace App\Soa;


use Core\Component\RPC\Common\AbstractPackageDecoder;

class Decoder extends AbstractPackageDecoder
{

    function decode($rawData)
    {
        // TODO: Implement decode() method.'
        return base64_decode($rawData);
    }
}
```
加密示例：
```
namespace App\Soa;


use Core\Component\RPC\Common\AbstractPackageEncoder;

class Encoder extends AbstractPackageEncoder
{

    function encode($rawData)
    {
        // TODO: Implement encode() method.
        return base64_encode($rawData);
    }
}
```
### 为服务配置对象设置消息加解密
```
//use  \Core\Component\RPC\Common\Config;
//$conf = new Config();
$conf->setPackageDecoder(new Decoder());
$conf->setPackageEncoder(new Encoder());
```

> 注意，SOA服务端对回调函数的处理是在swoole worker 中立即执行的，若有繁重的任务，请在回调函数内，将任务提交给异步进程处理，以避免服务端阻塞。



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
