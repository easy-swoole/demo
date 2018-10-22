# WebSocket控制器

EasySwoole 3.x支持以控制器模式来开发你的代码。

首先，修改项目根目录下配置文件dev.env，修改SERVER_TYPE为:
```php
MAIN_SERVER.SERVER_TYPE = WEB_SOCKET_SERVER ## 可选为 SERVER  WEB_SERVER WEB_SOCKET_SERVER
```
并且引入 easyswoole/socket composer 包:
>  *composer require easyswoole/socket*
*警告：请保证你安装的 easyswoole/socket 版本大于等于 1.0.7 否则会导致ws消息发送客户端无法解析的问题* 

## 新人帮助

* 本文遵循PSR-4自动加载类规范，如果你还不了解这个规范，请先学习相关规则。
* 本节基础命名空间App 默认指项目根目录下Application文件夹，如果你的App指向不同，请自行替换。
* 只要遵循PSR-4规范，无论你怎么组织文件结构都没问题，本节只做简单示例。

## 实现命令解析

**新人提示**
> 这里的命令解析，其意思为根据请求信息解析为具体的执行命令;
>
> 在easyswoole中，可以让WebSocket像传统框架那样按照控制器->方法这样去解析请求;
>
> 需要实现EasySwoole\Socket\AbstractInterface\ParserInterface;接口中的decode 和encode方法;

**创建Application/Socket/WebSocketParser.php文件，写入以下代码**

```php
namespace App\Socket;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Client;
use EasySwoole\Socket\Bean\{
    Caller,
    Response
};

use App\Socket\Websocket\Test;

class WebSocketParser implements ParserInterface
{
    /**
     * decode
     * @param  string         $raw    客户端消息
     * @param  Client         $client Socket Client 对象
     * @return Caller         Socket 调用对象
     */
    public function decode($raw, $client) : ? Caller
    {
        // 开发者在这里将客户端发送来的消息解析成具体的调用控制器和方法
        // 开发者可以自己选择 event 模式 或者传统的控制器模式
        $jsonObject = json_decode($raw);

        // new 调用者对象
        $caller =  new Caller();
        // 设置被调用的类
        $caller->setControllerClass(Test::class);
        // 设置被调用的方法
        $caller->setAction($jsonObject->action);
        // 设置被调用的Args
        $caller->setArgs(isset($jsonObject->content) ? [$jsonObject->content] : []);
        return $caller;
    }

    /**
     * encode
     * @param  Response $response Socket Response 对象
     * @param  Client   $client   Socket Client 对象
     * @return string             发送给客户端的消息
     */
    public function encode(Response $response, $client) : ? string
    {
        // 这里返回响应给客户端的信息
        // 这里应当只做统一的encode操作 具体的状态等应当由 Controller处理
        return json_encode($response->getResult());
    }
}
```
> *注意，请按照你实际的规则实现，本测试代码与前端代码对应。*

## 注册服务

**新人提示**
> 如果你尚未明白easyswoole运行机制，那么这里你简单理解为，当easyswoole运行到一定时刻，会执行以下方法。
>
> 这里是指注册你上面实现的解析器。

**在根目录下EasySwooleEvent.php文件mainServerCreate方法下加入以下代码**

```php
//注意：在此文件引入以下命名空间
use EasySwoole\Socket\Dispatcher;
use App\Socket\WebSocketParser;

public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    /**
     * *************** WebSocket ***************
     */

    // 创建一个 Dispatcher 配置
    $conf = new \EasySwoole\Socket\Config();
    // 设置 Dispatcher 为 WebSocket 模式
    $conf->setType($conf::WEB_SOCKET);
    // 设置解析器对象
    $conf->setParser(new WebSocketParser());

    // 创建 Dispatcher 对象 并注入 config 对象
    $dispatch = new Dispatcher($conf);

    // 给server 注册相关事件 在 WebSocket 模式下  message 事件必须注册 并且交给 Dispatcher 对象处理
    $register->set('message', function(\swoole_websocket_server  $server, \swoole_websocket_frame $frame) use ($dispatch){
        $dispatch->dispatch($server, $frame->data, $frame);
    });
}
```

> 在EasySwooleEvent中注册该服务。

## 测试前端代码

**友情提示**
> easyswoole 提供了更强大的WebSocket调试工具，[foo]: http://www.evalor.cn/websocket.html  'WEBSOCKET CLIENT'；

**创建Application/HttpController/websocket.html文件，写入以下代码**

```html
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div>
        <p>info below</p>
        <ul  id="line">

        </ul>
    </div>
    <div>
        <select id="action">
            <option value="who">who</option>
            <option value="hello">hello</option>
            <option value="delay">delay</option>
            <option value="404">404</option>
        </select>
        <input type="text" id="says">
        <button onclick="say()">发送</button>
    </div>
</div>
</body>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    var wsServer = 'ws://127.0.0.1:9501';
    var websocket = new WebSocket(wsServer);
    window.onload = function () {
        websocket.onopen = function (evt) {
            addLine("Connected to WebSocket server.");
        };

        websocket.onclose = function (evt) {
            addLine("Disconnected");
        };

        websocket.onmessage = function (evt) {
            addLine('Retrieved data from server: ' + evt.data);
        };

        websocket.onerror = function (evt, e) {
            addLine('Error occured: ' + evt.data);
        };
    };
    function addLine(data) {
        $("#line").append("<li>"+data+"</li>");
    }
    function say() {
        var content = $("#says").val();
        var action = $("#action").val();
        $("#says").val('');
        websocket.send(JSON.stringify({
            action:action,
            content:content
        }));
    }
</script>
</html>
```

## 测试用HttpController 视图控制器

**新人提示**
> 这里仅提供了前端基本的示例代码，更多需求根据自己业务逻辑设计

**创建Application/HttpController/WebSocketTest.php文件，写入以下代码**

```php
namespace App\HttpController;


use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Swoole\ServerManager;

class WebSocketTest extends Controller
{
    function index()
    {
        $content = file_get_contents(__DIR__.'/websocket.html');
        $this->response()->write($content);
    }
}
```
> 本控制器主要为方便你获得前端页面和从HTTP请求中对websocket 做推送。

## WebSocket 控制器

**新人提示**
> WebSocket控制器必须继承EasySwoole\Socket\AbstractInterface\Controller;
>
> actionNotFound方法提供了当找不到该方法时的返回信息，默认会传入本次请求的actionName。

**创建Application/Socket/WebSocket/Test.php文件，写入以下内容**

```php
namespace App\Socket\WebSocket;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

class Test extends Controller
{
    function hello()
    {
        $this->response()->setResult(['call hello with arg:'. json_encode($this->caller()->getArgs())]);
    }

    public function who(){
        $this->response()->setResult(['your fd is '. $this->caller()->getClient()->getFd()]);
    }

    function delay()
    {
        $this->response()->setResult(['this is delay action']);
        $client = $this->caller()->getClient();

        // 异步推送, 这里直接 use fd也是可以的
        // TaskManager::async 回调参数中的代码是在 task 进程中执行的 默认不含连接池 需要注意可能出现 getPool null的情况
        TaskManager::async(function () use ($client){
            $server = ServerManager::getInstance()->getSwooleServer();
            $i = 0;
            while ($i < 5) {
                sleep(1);
                $server->push($client->getFd(),'push in http at '.time());
                $i++;
            }
        });
    }
}

```
##测试

*如果你按照本文配置，那么你的文件结构应该是以下形式*

Application  
|---|HttpController  
|---|---|WebSocketTest.php  
|---|---|websocket.html  
|---|Socket
|---|---|WebSocket   
|---|---|---|Test.php  
|---|WebSocketParser.php  

> 首先在根目录运行easyswoole
>
> > *php easyswoole start*
>
> 如果没有错误此时已经启动了easyswoole服务;  
> 访问 127.0.0.1:9501/WebSocketTest/index 可以看到之前写的测试html文件;
> *新人提示：这种访问方式会请求HttpController控制器下Index.php中的index方法*  

##扩展

###自定义解析器

在上文的 WebSocketParser.php 中，已经实现了一个简单解析器；
我们可以通过自定义解析器，实现自己需要的场景。

```php
    public function decode($raw, $client) : ? Caller
    {
        $jsonObject = json_decode($raw);

        // new 调用者对象
        $caller =  new Caller();
        // 设置被调用的类 这里会将ws消息中的 class 参数解析为具体想访问的控制器
        // 如果更喜欢 event 方式 可以自定义 event 和具体的类的 map 即可
        // 注 目前 easyswoole 3.0.4 版本及以下 不支持直接传递 class string 可以通过这种方式
        $class = '\\App\\Socket\\Websocket\\'. ucfirst($jsonObject->class) ?? 'Test';
        $caller->setControllerClass($class);

        // 提供一个事件风格的写法
        // $eventMap = [
        //     'test' => Test::class,
        //     'index' => Index::class
        // ];
        // $caller->setControllerClass($eventMap[$jsonObject->event] ?? Test::class);

        // 设置被调用的方法
        $caller->setAction($jsonObject->action);
        // 设置被调用的Args
        $caller->setArgs($jsonObject->content ? [$jsonObject->content] : []);
        return $caller;
    }
  ```
  > 例如{"class":"Test","action":"hello"}  
  > 则会访问Application/Socket/WebSocket/Test.php 并执行hello方法

  **当然这里是举例，你可以根据自己的业务场景进行设计**
