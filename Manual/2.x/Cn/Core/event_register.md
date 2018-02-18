## 事件注册

#### 命名空间地址

EasySwoole\Core\Swoole\EventRegister

### 事件列表：

- onStart
- onShutdown
- onWorkerStart
- onWorkerStop
- onWorkerExit
- onTimer
- onConnect
- onReceive
- onPacket
- onClose
- onBufferFull
- onBufferEmpty
- onTask
- onFinish
- onPipeMessage
- onWorkerError
- onManagerStart
- onManagerStop
- onRequest
- onHandShake
- onMessage
- onOpen

### 方法列表

事件注册：

- string `key` 事件
- function `item` 闭包处理的内容 

```php
function add($key, $item): EventRegister
```

获得某个注册事件：

```php
public function get($key)
```

事件注册：（同add方法一样，只是该方法不返回this）

- string `key` 事件
- function `item` 闭包处理的内容 

```php
function withAdd($key, $item)
```

获得所有注册的事件：

```php
function all(): array
```

注册默认请求事件：

- string `controllerNameSpace ` 控制器命名空间，如需改变可继承修改

```php
public function registerDefaultOnRequest($controllerNameSpace = 'App\\HttpController\\'):void
```

注册默认任务完成事件：

```php
public function registerDefaultOnFinish():void
```

注册默认客户端接收触发事件：

- ParserInterface `parser` 解析器
- callable `onError` 错误回调函数
- ExceptionHandler `exceptionHandler` 异常处理类

```php
public function registerDefaultOnReceive(ParserInterface $parser,callable $onError = null,ExceptionHandler $exceptionHandler = null):void
```

注册默认数据接收事件：

- ParserInterface `parser` 解析器
- callable `onError` 错误回调函数
- ExceptionHandler `exceptionHandler` 异常处理类

```php
public function registerDefaultOnPacket(ParserInterface $parser,callable $onError = null,ExceptionHandler $exceptionHandler = null)
```

注册默认服务器收到来自客户端的数据帧时会回调事件：

- ParserInterface `parser` 解析器
- callable `onError` 错误回调函数
- ExceptionHandler `exceptionHandler` 异常处理类

```php
public function registerDefaultOnMessage(ParserInterface $parser,callable $onError = null,ExceptionHandler $exceptionHandler = null)
```

## 示例

```php
<?php

namespace EasySwoole;

use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;

use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\EventRegister;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\AbstractInterface\EventInterface;


class EasySwooleEvent implements EventInterface
{
    public function frameInitialize() : void
    {
        // 设置错误处理类
        Di::getInstance()->set( SysConst::ERROR_HANDLER, \yourApp\ErrorHandler::class );
        // 设置异常处理类
        Di::getInstance()->set( SysConst::HTTP_EXCEPTION_HANDLER, \yourApp\ExceptionHandler::class );
        // 设置进程关闭处理类
        Di::getInstance()->set( SysConst::SHUTDOWN_FUNCTION, \yourApp\ShutdownHandler::class );
    }
	
    // 主进程创建事件时调用的函数
    public function mainServerCreate( ServerManager $server, EventRegister $register ) : void
    {
        // 你可以单独弄个类来管理你项目默认要初始化的一些代码。
        \yourApp\Manager::run();
        
      
		// 添加websocket的onMessage事件
        $register->add( "message", function( \swoole_websocket_server $server, \swoole_websocket_frame $frame ){
            if( $frame->data == 'pong' ){
                $server->push( $frame->fd, json_encode( ['type' => 'pong', 'code' => 0, 'msg' => '服务器端保持心跳'] ) );
            }
        } );
		
        // 添加websocket的onOpen事件
        $register->add( 'open', function( \swoole_websocket_server $server, \swoole_http_request $request ){
            $server->push( $request->fd, json_encode( ['type' => 'open', 'code' => 0, 'msg' => '服务器请求连接'] ) );
        } );
		
        // 添加websocket的onClose事件
        $register->add( 'close', function( \swoole_server $server, int $fd, int $reactorId ){
            $info = $server->connection_info( $fd );
            // 以免报错，先判断这个客户端链接是否还存在
            if( isset( $info['websocket_status'] ) && $info['websocket_status'] === 3 ){
                $server->push( $fd, json_encode( ['type' => 'close', 'code' => 0, 'msg' => '服务器链接关闭'] ) );
            }
        } );

    }
	// 当http请求时要执行的事件
    public function onRequest( Request $request, Response $response ) : void
    {
    }

    // http请求完毕后要执行的事件
    public function afterAction( Request $request, Response $response ) : void
    {
    }
}


```

