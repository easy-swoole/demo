# WebSocket聊天室示例

本示例将演示如何使用 `easySwoole` 进行WebSocket聊天室开发，阅读本篇前，请先阅读文档相关部分。  

-   **本示例依赖Redis,请自行安装Redis及Redis扩展**
-   **本文所有文件命名空间及文件结构请自行根据业务情况修改。**

# 一、创建WebSocket服务器

## 配置Config.php

在easySwoole的根目录中，Config.php是easySwoole的配置文件，可以使用Config对象获取其中的配置。

-   本示例需要在Config.php中设置 `SERVER_TYPE` 为 `TYPE_WEB_SOCKET_SERVER`模式。
-   本示例需要在Config.php中新增 `REDIS` 配置项。

_更改SERVER_TYPE_

```php
'SERVER_TYPE'=>\EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SOCKET_SERVER,
```

_新增REDIS配置_

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

_例如上面的JSON数据,意思为访问Test控制器中的Index方法,参数为 `parameter_one` 和 `parameter_two`_  

easySwoole已经内置了基本的WebSocket Server封装，我们只需要实现 `EasySwoole\Core\Socket\AbstractInterface\ParserInterface` 解析器接口即可。

_示例代码_

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

-   如果能正常连接服务器，说明Server已经启动
-   如果发送 `空` 字符串消息返回 `unknown command` 说明解析器已经工作
-   如果发送 `{"controller": "Test","action": "index"}` 返回 `you fd is 1` 则说明Server正常工作

**到此为止WebSocket Server已经可以完成基本的工作，接下来是在easySwoole中使用Redis。**

# 二、 在easySwoole中使用Redis

## 建立Redis连接

_easySwoole中提供了Redis连接池，但是本示例不使用此方案，有能力的请自行选择。_

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

-   如果发送`{"controller": "Test","action": "index"}`返回 `1 这是一个测试` ，则说明Redis连接正常。

_至此已经完成了Redis的基本使用，以下为业务部分_

# 三、 聊天室设计

## 基本定义

-   `fd` : 连接id,Server发送消息的唯一标识,会回收,不会重复。
-   `userId` : 用户id,不多赘述。
-   `roomId` : 房间id,房间的唯一标识。

_实际上聊天室就是对 `fd` `userId` `roomId` 的管理_

## 设计思路

### 私聊

私聊实际上是指fd和uid的关系，即通过uid查询fd，发送消息。

使用Redis sorted set(有序集合)来管理 `fd` 和 `userId`之间的关系。

| key    | socre  | member |
| :----- | :----- | :----- |
| online | userId | fd     |

### 全服务器广播

全服务器广播实际上是给全部fd连接发送消息，可以使用上面的online有序集合遍历发送，也可以直接遍历server->connections中的fd发送(推荐)

### 房间消息

房间消息其实是指发送信息到具体房间中的一个概念，房间只是fd的一种组织(管理)形式，在房间这个概念中，实际上并不需要uid这个概念，因为你在公会频道收不到队伍消息嘛。

我们只需要映射好room_id和fd的关系即可实现房间消息功能，这里我们选择Redis Hash(哈希)数据结构来维护此关系。

| key    | field  | value  |
| :----- | :----- | :----- |
| roomId | userId | fd     |

Hash允许你通过key只查询field列或者只查询value列，这样你就可以实现查询用户是否在房间(用于业务层面的检查)和房间内全部fd；随后通过迭代(遍历)，value列来发送信息。

### 回收fd

由于用户断线时，我们只能获取到fd，并不能获取到roomId和userId，所以我们必须设计一套回收机制，保证Redis中的映射关系不错误；防止信息发送给错误的fd。

在上面我们其实已经建立了userId => fd 的映射关系，双向都能够找到找到对应彼此的值，唯独缺少了 roomId => fd的关系映射，在这里我们通过再建立一组关系映射，来保障fd => roomId的映射关系，由于fd是不重复的，roomId是重复的，故可以直接使用 `有序集合` 来管理。

| key    | socre  | member |
| :----- | :----- | :----- |
| rfMap  | roomId | fd     |

## 代码实现

**注意：以下代码均是基本逻辑，业务使用需要根据自己业务场景丰富**

### Room基本逻辑

```php
<?php
namespace App\Socket\Logic;

use EasySwoole\Core\Component\Di;


class Room
{
    /**
     * 获取Redis连接实例
     * @return object Redis
     */
    protected static function getRedis()
    {
        return Di::getInstance()->get('REDIS')->handler();
    }

    /**
     * 进入房间
     * @param  int    $roomId 房间id
     * @param  int    $userId userId
     * @param  int    $fd     连接id
     * @return
     */
    public static function joinRoom(int $roomId, int $fd)
    {
        $userId = self::getUserId($fd);
        self::getRedis()->zAdd('rfMap', $roomId, $fd);
        self::getRedis()->hSet("room:{$roomId}", $userId, $fd);
    }

    /**
     * 登录
     * @param  int    $userId 用户id
     * @param  int    $fd     连接id
     * @return bool
     */
    public static function login(int $userId, int $fd)
    {
        self::getRedis()->zAdd('online', $userId, $fd);
    }

    /**
     * 获取用户id
     * @param  int    $fd
     * @return int    userId
     */
    public static function getUserId(int $fd)
    {
        return self::getRedis()->zScore('online', $fd);
    }

    /**
     * 获取用户fd
     * @param  int    $userId
     * @return array         用户fd集
     */
    public static function getUserFd(int $userId)
    {
        return self::getRedis()->zRange('online', $userId, $userId, true);
    }

    /**
     * 获取RoomId
     * @param  int    $fd
     * @return int    RoomId
     */
    public static function getRoomId(int $fd)
    {
        return self::getRedis()->zScore('rfMap', $fd);
    }

    /**
     * 获取room中全部fd
     * @param  int    $roomId roomId
     * @return array         房间中fd
     */
    public static function selectRoomFd(int $roomId)
    {
        return self::getRedis()->hVals("room:{$roomId}");
    }

    /**
     * 退出room
     * @param  int    $roomId roomId
     * @param  int    $fd     fd
     * @return
     */
     public static function exitRoom(int $roomId, int $fd)
     {
         $userId = self::getUserId($fd);
         self::getRedis()->hDel("room:{$roomId}", $userId);
         self::getRedis()->zRem('rfMap', $fd);
     }

    /**
     * 关闭连接
     * @param  string $fd 链接id
     */
    public static function close(int $fd)
    {
        $roomId = self::getRoomId($fd);
        self::exitRoom($roomId, $fd);
        self::getRedis()->zRem('online', $fd);
    }
}

```

### Test测试用控制器

```php
<?php
namespace App\Socket\Controller\WebSocket;

use EasySwoole\Core\Socket\AbstractInterface\WebSocketController;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;

use App\Socket\Logic\Room;

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
    }

    /**
     * 进入房间
     */
    public function intoRoom()
    {
        // TODO: 业务逻辑自行实现
        $param = $this->request()->getArg('data');
        $userId = $param['userId'];
        $roomId = $param['roomId'];

        $fd = $this->client()->getFd();
        Room::login($userId, $fd);
        Room::joinRoom($roomId, $fd);
        $this->response()->write("加入{$roomId}房间");
    }

    /**
     * 发送信息到房间
     */
    public function sendToRoom()
    {
        // TODO: 业务逻辑自行实现
        $param = $this->request()->getArg('data');
        $message = $param['message'];
        $roomId = $param['roomId'];

        //异步推送
        TaskManager::async(function ()use($roomId, $message){
            $list = Room::selectRoomFd($roomId);
            foreach ($list as $fd) {
                ServerManager::getInstance()->getServer()->push($fd, $message);
            }
        });
    }

    /**
     * 发送私聊
     */
    public function sendToUser()
    {
        // TODO: 业务逻辑自行实现
        $param = $this->request()->getArg('data');
        $message = $param['message'];
        $userId = $param['userId'];

        //异步推送
        TaskManager::async(function ()use($userId, $message){
            $fdList = Room::getUserFd($userId);
            foreach ($fdList as $fd) {
                ServerManager::getInstance()->getServer()->push($fd, $message);
            }
        });
    }
}
```

### 注册连接关闭事件

```php
// 引入EventHelper
use \EasySwoole\Core\Swoole\EventHelper;
// 引入Di
use \EasySwoole\Core\Component\Di;
// 注意这里是指额外引入我们上文实现的解析器
use \App\Socket\Parser\WebSocket;
// 引入上文Redis连接
use \App\Utility\Redis;
// 引入上文Room文件
use \App\Socket\Logic\Room;

// ...省略
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // 注册WebSocket解析器
    EventHelper::registerDefaultOnMessage($register, WebSocket::class);
    //注册onClose事件
    $register->add($register::onClose, function (\swoole_server $server, $fd, $reactorId) {
        //清除Redis fd的全部关联
        Room::close($fd);
    });
    // 注册Redis
    Di::getInstance()->set('REDIS', new Redis(Config::getInstance()->getConf('REDIS')));
}
```
