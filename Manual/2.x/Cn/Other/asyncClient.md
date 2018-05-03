# EasySwoole中使用异步客户端
为方便测试，我们以RPC中的例子来实现服务端，具体请看文档RPC章节。
## 纯原生异步
```php
    public static function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        $tcp = $server->addServer('tcp',9502);
        $tcp->registerDefaultOnReceive(new \Tcp\Parser());
        $register->add($register::onWorkerStart,function ($ser,$workerId){
            if($workerId == 0){
                $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
                $client->on("connect", function(\swoole_client $cli) {
                    $cli->send("test:delay");
                });
                $client->on("receive", function(\swoole_client $cli, $data){
                    echo "Receive: $data";
                    $cli->send("test:delay");
                    sleep(1);
                });
                $client->on("error", function(\swoole_client $cli){
                    echo "error\n";
                });
                $client->on("close", function(\swoole_client $cli){
                    echo "Connection close\n";
                });
                $client->connect('127.0.0.1', 9502);
            }
        });
    }
```

## 伪异步-eventLoop
利用swoole自带的事件循环，实现异步
```php

    public static function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        $tcp = $server->addServer('tcp',9502);
        $tcp->registerDefaultOnReceive(new \Tcp\Parser());
        $register->add($register::onWorkerStart,function ($ser,$workerId){
            if($workerId == 0){
                $client = new \swoole_client(SWOOLE_SOCK_TCP);
                $client->connect('127.0.0.1', 9502);
                //该出send是为了触发服务端主动返回消息，方便直观测试
                $client->send("test:delay");
                swoole_event_add($client->sock,function()use($client){
                    //服务端中，在\Tcp\Parser中，因为你发test:delay命令，是依旧会先给你返回\n,因此请做下空判定
                    $data = trim($client->recv());
                    if(!empty($data)){
                        var_dump('rec from ser');
                        $client->send("test:delay");
                    }
                });
            }
        });
    }
```
## 伪异步-socket select

```php
    public static function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        $tcp = $server->addServer('tcp',9502);
        $tcp->registerDefaultOnReceive(new \Tcp\Parser());
        $register->add($register::onWorkerStart,function ($ser,$workerId){
            if($workerId == 0){
                $client = new \swoole_client(SWOOLE_SOCK_TCP);
                $client->connect('127.0.0.1', 9502);
                //该出send是为了触发服务端主动返回消息，方便直观测试
                $client->send("test:delay");
                Timer::loop(100,function ()use($client){
                    $write = $error = array();
                    $read = [$client];
                    $n = swoole_client_select($read, $write, $error, 0.01);
                    if($n > 0){
                        $data = trim($client->recv());
                        if(!empty($data)){
                            $client->send("test:delay");
                            var_dump('rec:'.$data);
                        }
                    }
                });
            }
        });
    }
```