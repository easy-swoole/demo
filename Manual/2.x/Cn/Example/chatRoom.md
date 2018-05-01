# WebSocket聊天室示例

本示例将演示如何使用 `easySwoole` 进行WebSocket聊天室开发，阅读本篇前，请先阅读文档相关部分。  
- **本示例依赖Redis,请自行安装Redis及Redis扩展**
- **本文所有文件命名空间及文件结构请自行根据业务情况修改。**

# 一、创建WebSocket服务器  
## 配置Config.php
在easySwoole的根目录中，Config.php是easySwoole的配置文件，可以使用Config对象获取其中的配置。
- 本示例需要在Config.php中设置 `SERVER_TYPE` 为 `TYPE_WEB_SOCKET_SERVER`模式。
- 本示例需要在Config.php中新增 `REDIS` 配置项。

*更改SERVER_TYPE*
```php
'SERVER_TYPE'=>\EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SOCKET_SERVER,
```

*新增REDIS配置*
```php
'REDIS' => [
    'host'       => '127.0.0.1',
    'port'       => 6379,
    'password'   => '',
    'select'     => 0,
    'timeout'    => 0,
    'expire'     => 0,
    'persistent' => false,
    'prefix'     => '',
]
```

## 自定义WebSocket解析器
WebSocket模式下，Client和Server之间不再是新的请求，而是一条条消息；所以我们通过约定的格式来发送和响应消息，从而实现各种各样的功能。
通常传递自定义消息的方式是JSON和XML,在这里我们选择更方便简单的JSON作为示例；我们定义JSON数据3个键。  

```json
{
    "controller": "Test",
    "action": "index",
    "data": {
        "parameter_one": "数据one",
        "parameter_two": "数据two"
    }
}
```
*例如上面的JSON数据,意思为访问Test控制器中的Index方法,参数为 `parameter_one` 和 `parameter_two`*  

easySwoole已经内置了基本的WebSocket Server封装，我们只需要实现 `EasySwoole\Core\Socket\AbstractInterface\ParserInterface` 解析器接口即可。

*示例代码*

```php
<?php
namespace App\Socket\Parser;

use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

use App\Socket\Controller\WebSocket\Index;

class WebSocket implements ParserInterface
{

    public static function decode($raw, $client)
    {
        //检查数据是否为JSON
        $commandLine = json_decode($raw, true);
        if (!is_array($commandLine)) {
            return 'unknown command';
        }

        $CommandBean = new CommandBean();
        $control = isset($commandLine['controller']) ? 'App\\Socket\\Controller\\WebSocket\\'. ucfirst($commandLine['controller']) : '';
        $action = $commandLine['action'] ?? 'none';
        $data = $commandLine['data'] ?? null;
        //找不到类时访问默认Index类
        $CommandBean->setControllerClass(class_exists($control) ? $control : Index::class);
        $CommandBean->setAction(class_exists($control) ? $action : 'controllerNotFound');
        $CommandBean->setArg('data', $data);

        return $CommandBean;
    }

    public static function encode(string $raw, $client, $commandBean): ?string
    {
        // TODO: Implement encode() method.
        return $raw;
    }
}
```

在上面的decode方法中，我们将一条JSON信息解析成调用 `'App\\Socket\\Controller\\WebSocket\\'` 命名空间下的控制器和方法，就像我们使用传统FPM模式那样。

## 注册WebSocket解析器
在easySwoole根目录中，EasySwooleEvent.php是easySwoole开放的事件注册方法，你可以简单的理解为，当程序执行到一些特定时刻，会执行Event中的方法。

我们需要在 `mainServerCreate` (主服务创建时)方法中注册我们上面的WebSocket解析器。

```php
// 引入EventHelper
use \EasySwoole\Core\Swoole\EventHelper;
// 注意这里是指额外引入我们上文实现的解析器
use \App\Socket\Parser\WebSocket;

//...省略
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // 注意一个事件方法中可以注册多个服务，这里只是注册WebSocket解析器
    // 注册WebSocket解析器
    EventHelper::registerDefaultOnMessage($register, WebSocket::class);
}
```

接下来我们创建一个Test类来测试我们的WebSocket Server

```php
<?php
namespace App\Socket\Controller\WebSocket;

use EasySwoole\Core\Socket\AbstractInterface\WebSocketController;

class Test extends WebSocketController
{

    /**
     * 访问找不到的action
     * @param  ?string $actionName 找不到的name名
     * @return string
     */
    public function actionNotFound(?string $actionName)
    {
        $this->response()->write("action call {$actionName} not found");
    }

    public function index()
    {
        $fd = $this->client()->getFd();
        $this->response()->write("you fd is {$fd}");
    }
}
```

现在可以启动我们的Server了，在easySwoole根目录中输入以下命令启动。  
> php easyswoole start  
如果没有任何报错，那么已经启动了Server；这里我推荐<a href="http://www.evalor.cn/websocket.html">WEBSOCKET CLIENT
</a>测试工具来测试我们的Server。

- 如果能正常连接服务器，说明Server已经启动
- 如果发送空字符串消息返回unknown command说明解析器已经工作
- 如果发送{"controller": "Test","action": "index"}返回you fd is 1则说明Server正常工作

**到此为止WebSocket Server已经可以完成基本的工作，接下来将开始实现业务部分。**
