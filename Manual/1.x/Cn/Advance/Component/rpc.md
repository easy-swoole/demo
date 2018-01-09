# RPC & SOA
EasySwoole 同样可以做串行、并行化的SOA服务调用，底层基于SWOOLE_TCP实现，支持自定义消息加解密，为方便多种客户端（不同语言）调用，服务交互采用json格式传递，开发者可以快速以其他语言实现。
## 场景描述
例如，某个应用中，A为前端承载机器，B与C分别部署着不同的服务。一个用户请求进来，A同时向BC发起请求获取，并汇集两个结果返回给用户。

在EasySwoole中，RPC服务以服务名=>多个行为名的形式存在。

## 创建服务命令注册类
```
namespace App\RPC;


use Core\Component\RPC\AbstractInterface\AbstractActionRegister;
use Core\Component\RPC\Common\ActionList;
use Core\Component\RPC\Common\Package;

class Goods extends AbstractActionRegister
{

    function register(ActionList $actionList)
    {
        // TODO: Implement register() method.
        $actionList->setDefaultAction(function (Package $req,Package $res){
            $res->setMessage('this is goods default');
        });
    }
}
```
```
namespace App\RPC;


use Core\Component\RPC\AbstractInterface\AbstractActionRegister;
use Core\Component\RPC\Common\ActionList;
use Core\Component\RPC\Common\Package;
use Core\Component\Socket\Client\TcpClient;

class User extends AbstractActionRegister
{

    function register(ActionList $actionList)
    {
        // TODO: Implement register() method.
        $actionList->registerAction('who', function (Package $req, Package $res, TcpClient $client) {
            var_dump('your req package is' . $req->__toString());
            $res->setMessage('this is User.who');
        });


        $actionList->registerAction('login', function (Package $req, Package $res, TcpClient $client) {
            var_dump('your req package is' . $req->__toString());
            $res->setMessage('this is User.login');
        });

        $actionList->setDefaultAction(function (Package $req, Package $res, TcpClient $client) {
            $res->setMessage('this is user.default');
        });

    }
}
```
```
namespace App\RPC;


use Core\Component\RPC\AbstractInterface\AbstractActionRegister;
use Core\Component\RPC\Common\ActionList;
use Core\Component\RPC\Common\Package;
use Core\Component\Socket\Client\TcpClient;

class User2 extends AbstractActionRegister
{

    function register(ActionList $actionList)
    {
        // TODO: Implement register() method.
        $actionList->registerAction('who', function (Package $req, Package $res, TcpClient $client) {
            var_dump('your req package is' . $req->__toString());
            $res->setMessage('this is User.who');
        });


        $actionList->registerAction('login', function (Package $req, Package $res, TcpClient $client) {
            var_dump('your req package is' . $req->__toString());
            $res->setMessage('this is User.login');
        });

        $actionList->setDefaultAction(function (Package $req, Package $res, TcpClient $client) {
            $res->setMessage('this is user.default');
        });

    }
}

```
> 以上代码，实现了三个不同服务的命令注册。

## 监听服务
在服务启动前事件中，进行服务注册。
```
function beforeWorkerStart(\swoole_server $server)
{
        // TODO: Implement beforeWorkerStart() method.
        $conf = new \Core\Component\RPC\Common\Config();
        $server = new Server($conf);
        $server->registerServer('user')->setActionRegisterClass(User::class);
        $server->registerServer('goods')->setActionRegisterClass(Goods::class);
        $server->attach(9502);

        $server2 = new Server($conf);
        $server2->registerServer('user')->setActionRegisterClass(User2::class);
        $server2->attach(9503);
}
```

## 调用客户端
```
use Core\Component\RPC\Common\Package;
$conf = new \Core\Component\RPC\Common\Config();
$conf->setPort(9502);
$conf->setHost('127.0.0.1');

$client = new \Core\Component\RPC\Client\Client();
$server1 = $client->selectServer($conf);
$server1->addCall('user','who',null,function (Package $req,Package $res){
    echo "call success at".$res->__toString()."\n";
},function (Package $req,Package $res){
    echo "call fail at".$res->__toString()."\n";
});

$server1->addCall("user",'login',[1,2,3,4],function (Package $req,Package $res){
    echo "call success at".$res->__toString()."\n";
});

$server1->addCall("user",'404',[1,2,3,4],function (Package $req,Package $res){
    echo "call success at".$res->__toString()."\n";
});

$server1->addCall('goods','404',null,function (){
    echo "success"."\n";
});

$conf2 = new \Core\Component\RPC\Common\Config();
$conf2->setHost('127.0.0.1');
$conf2->setPort(9503);

$server2 = $client->selectServer($conf2);
$server2->addCall('user','404',null,function (){
    echo "success at server 2";
});

$client->call();

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
