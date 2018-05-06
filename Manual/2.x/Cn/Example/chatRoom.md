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

**注意： `EasySwooleEvent` 文件中的use下文都为省略模式，请自行引入其他必要组件**
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

如果没有任何报错，那么已经启动了Server；这里我推荐<a href="http://evalor.cn/websocket.html">WEBSOCKET CLIENT
</a>测试工具来测试我们的Server。

- 如果能正常连接服务器，说明Server已经启动
- 如果发送 `空` 字符串消息返回 `unknown command` 说明解析器已经工作
- 如果发送 `{"controller": "Test","action": "index"}` 返回 `you fd is 1` 则说明Server正常工作

**到此为止WebSocket Server已经可以完成基本的工作，接下来是在easySwoole中使用Redis。**

# 二、 在easySwoole中使用Redis
## 建立Redis连接

*easySwoole中提供了Redis连接池，但是本示例不使用此方案，有能力的请自行选择。*

php Redis连接示例

```php
<?php

namespace App\Utility;

class Redis
{
    protected static $instance = null;

    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];

    /**
     * 构造函数
     * @param array $options 参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * 连接Redis
     * @return void
     */
    protected function connect()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new \Redis;
            if ($this->options['persistent']) {
                self::$instance->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
            } else {
                self::$instance->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
            }

            if ('' != $this->options['password']) {
                self::$instance->auth($this->options['password']);
            }

            if (0 != $this->options['select']) {
                self::$instance->select($this->options['select']);
            }
        }
    }

    /**
     * 获取连接句柄
     * @return object Redis
     */
    public function handler()
    {
        $this->connect();
        return self::$instance;
    }
}
```

easySwoole提供了Di容器，可以方便我们随时取用Redis，现在让我们在Event事件中将Redis注入到Di容器中。

```php
// 引入EventHelper
use \EasySwoole\Core\Swoole\EventHelper;
// 引入Di
use \EasySwoole\Core\Component\Di;
// 注意这里是指额外引入我们上文实现的解析器
use \App\Socket\Parser\WebSocket;
// 引入上文Redis连接
use \App\Utility\Redis;

// ...省略
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // 注意一个事件方法中可以注册多个服务，这里只是注册WebSocket解析器
    // 注册WebSocket解析器
    EventHelper::registerDefaultOnMessage($register, WebSocket::class);
    // 注册Redis 从Config中读取Redis配置
    Di::getInstance()->set('REDIS', new Redis(Config::getInstance()->getConf('REDIS')));
}
```

## 创建Room.php并使用Redis
现在我们新建Room.php文件作为我们的房间逻辑实现文件，第一步是连接Redis并测试。

```php
<?php
namespace App\Socket\Logic;

use EasySwoole\Core\Component\Di;


class Room
{
    public static function getRedis()
    {
        return Di::getInstance()->get('REDIS')->handler();
    }

    public static function testSet()
    {
        return self::getRedis()->set('test', '这是一个测试');
    }

    public static function testGet()
    {
        return self::getRedis()->get('test');
    }
}
```

修改Test类的index方法用于测试

```php
<?php
namespace App\Socket\Controller\WebSocket;

use EasySwoole\Core\Socket\AbstractInterface\WebSocketController;

use App\Socket\Logic\Room;

class Test extends WebSocketController
{
    public function index()
    {
        $this->response()->write(Room::testSet());
        $this->response()->write("\n");
        $this->response()->write(Room::testGet());
    }
}
```

现在可以启动Server了，如果没有任何错误，请使用<a href="http://evalor.cn/websocket.html">WEBSOCKET CLIENT
</a>测试以下内容。  
- 如果发送`{"controller": "Test","action": "index"}`返回 `1 这是一个测试` ，则说明Redis连接正常。

*至此已经完成了Redis的基本使用，以下为业务部分*

# 三、 聊天室设计
## 基本定义

- `fd` : 连接id,Server发送消息的唯一标识,会回收,不会重复。
- `userId` : 用户id,不多赘述。
- `roomId` : 房间id,房间的唯一标识。

*实际上聊天室就是对 `fd` `userId` `roomId` 的管理*

## 设计思路
###私聊

私聊实际上是指fd和uid的关系，即通过uid查询fd，发送消息；这种结构是最基本的kv结构，也就是如下结构:
| uid      | fd     |
| -------- | -----: |
| 1        | 1      |
| 1        | 2      |
| 1        | 3      |

这里我们可以看出来如果直接使用Redis的`string` kv则会出现不可避免的冲突，即key相同的情况，而如果fd做key，则无法通过uid查询fd，故不可取。
这里我的做法是使用Redis`有序集合(sorted set)`来处理，有序集合有3个属性: `key(键)` `socre(分值)` `member(成员)`，并且member绝不重复，相同的member会被覆盖；而socre则可以重复。有序集合允许通过member查询socre(一对多)也允许使用socre范围查询member(多对多)。
看，简直是量身定做的，由于我们在业务层面保证了uid的绝对属主(fd每次连接都会随机分配，并不真的属主)，在这种情况下，实际上我们使用多对多查询其实是一对多的情况。
当我们需要想要给uid = 1的用户发送信息，只需要通过socre = 1 查出对应的member fd列表(即便你在不同的房间，不同的设备，你都需要收到私聊)，然后迭代(遍历)这个列表发送信息即可。
这个有序集合相当于全服务器所有的在线人数(全部连接数)，所以key可以叫做online即online集合。

| Redis概念      || key      || socre      | member     |
| :--------: | :--------: |:--------: |:--------: |:--------: |
| 业务概念        | online      |uid        | fd       |
| 业务概念        | online      |1        |1        |
| 业务概念        | online      |1        |2        |
| 业务概念        | online      |1        |3        |
| 业务概念        | online      |2        |4        |
