# EasySwoole中使用异步客户端
为方便查看代码,本文没有使用自定义进程类模板,如果需要开发,可查看[自定义进程](Process.md)  在run方法里面使用异步客户端
>请不要直接在worker进程使用自定义进程,否则将出现问题  
## 纯原生异步
```php
 public static function mainServerCreate(EventRegister $register)
  {
       
//        //纯原生异步
    ServerManager::getInstance()->getSwooleServer()->addProcess(new Process(function ($worker){
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
        $client->connect('192.168.159.1', 9502);
   
    
        //本demo自定义进程采用的是原生写法,如果需要使用,请使用自定义进程类模板开发
        if (extension_loaded('pcntl')) {//异步信号,使用自定义进程类模板不需要该代码
            pcntl_async_signals(true);
        }
        Process::signal(SIGTERM,function ()use($worker){//信号回调,使用自定义进程类模板不需要该代码
           $worker->exit(0);
        });
    }));
}
```

## 伪异步-eventLoop
利用swoole自带的事件循环，实现异步
```php

    public static function mainServerCreate(EventRegister $register)
    {
         ServerManager::getInstance()->getSwooleServer()->addProcess(new Process(function ($worker){
              $client = new \swoole_client(SWOOLE_SOCK_TCP);
              $client->connect('192.168.159.1', 9502);
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
              //本demo自定义进程采用的是原生写法,如果需要使用,请使用上文的自定义进程类模板开发
              if (extension_loaded('pcntl')) {//异步信号,使用自定义进程类模板不需要该代码
                  pcntl_async_signals(true);
              }
              Process::signal(SIGTERM,function ()use($worker){//信号回调,使用自定义进程类模板不需要该代码
                  $worker->exit(0);
              });
         }));
    }
```
## 伪异步-socket select

```php
    
 public static function mainServerCreate(EventRegister $register)
    {
         ServerManager::getInstance()->getSwooleServer()->addProcess(new Process(function ($worker){
               $client = new \swoole_client(SWOOLE_SOCK_TCP);
               $client->connect('192.168.159.1', 9502);
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
               //本demo自定义进程采用的是原生写法,如果需要使用,请使用上文的自定义进程类模板开发
               if (extension_loaded('pcntl')) {//异步信号,使用自定义进程类模板不需要该代码
                   pcntl_async_signals(true);
               }
               Process::signal(SIGTERM,function ()use($worker){//信号回调,使用自定义进程类模板不需要该代码
                  $worker->exit(0);
               });
           }));
    }
```