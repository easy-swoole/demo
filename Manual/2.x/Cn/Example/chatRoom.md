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
    "class": "Test",
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
namespace App\WebSocket;
use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

class Parser implements ParserInterface
{

    public static function decode($raw, $client)
    {
        // TODO: Implement decode() method.
        $CommandBean = new CommandBean();
        //这里的$raw是请求服务器的信息，你可以自行设计，这里使用了JSON字符串的形式。
        $commandLine = json_decode($raw, true);
        //这里会获取JSON数据中class键对应的值，并且设置一些默认值
        //当用户传递class键的时候，会去App/WebSocket命名空间下寻找类
        $control = isset($commandLine['class']) ? 'App\\WebSocket\\Controller\\'. ucfirst($commandLine['class']) : '';
        $action = $commandLine['action'] ?? 'none';
        $data = $commandLine['data'] ?? null;
        //先检查这个类是否存在，如果不存在则使用Index默认类
        $CommandBean->setControllerClass(class_exists($control) ? $control : App\Websocket\Controller\Index::class);
        //检查传递的action键是否存在，如果不存在则访问默认方法
        $CommandBean->setAction(class_exists($control) ? $action : 'controllerNotFound');
        $CommandBean->setArg('data', $data);
        return $CommandBean;

    }

    public static function encode(string $raw, $client): ?string
    {
        // TODO: Implement encode() method.
        /*
         * 注意，return ''与return null不一样，空字符串一样会回复给客户端，比如在服务端主动心跳测试的场景
         */
        if(strlen($raw) == 0){
            return null;
        }
        return $raw;
    }
}
}
```

在上面的decode方法中，我们将一条JSON信息解析成调用 `'App\\WebSocket\\Controller\\'` 命名空间下的控制器和方法，就像我们使用传统FPM模式那样。

## 注册WebSocket解析器

在easySwoole根目录中，EasySwooleEvent.php是easySwoole开放的事件注册方法，你可以简单的理解为，当程序执行到一些特定时刻，会执行Event中的方法。

**注意： `EasySwooleEvent` 文件中的use下文都为省略模式，请自行引入其他必要组件**
我们需要在 `mainServerCreate` (主服务创建时)方法中注册我们上面的WebSocket解析器。

```php
// 引入EventHelper
use \EasySwoole\Core\Swoole\EventHelper;
// 注意这里是指额外引入我们上文实现的解析器
use \App\WebSocket\Parser as WebSocketParser;

//...省略
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // 注意一个事件方法中可以注册多个服务，这里只是注册WebSocket解析器
    // // 注册WebSocket处理
    EventHelper::registerDefaultOnMessage($register, WebSocketParser::class);
}
```

接下来我们创建一个Test类来测试我们的WebSocket Server

```php
<?php
namespace App\WebSocket\Controller;

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
-   如果发送 `{"class": "Test","action": "index"}` 返回 `you fd is 1` 则说明Server正常工作

**到此为止WebSocket Server已经可以完成基本的工作，接下来是在easySwoole中使用Redis。**

# 二、 在easySwoole中使用Redis

## 建立Redis连接

_easySwoole中提供了Redis连接池，但是本示例不使用此方案，有能力的请自行选择。_
_基于Redis连接池的示例将写在后文，但不推荐无经验的用户使用。_

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
use \App\WebSocket\Parser as WebSocketParser;
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

## 创建Im.php并使用Redis

现在我们新建Im.php文件作为我们的房间逻辑实现文件，第一步是连接Redis并测试。

```php
<?php
namespace App\WebSocket\Logic;

use EasySwoole\Core\Component\Di;


class Im
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
namespace App\WebSocket\Controller;

use EasySwoole\Core\Socket\AbstractInterface\WebSocketController;

use App\WebSocket\Logic\Im;

class Test extends WebSocketController
{
    public function index()
    {
        $this->response()->write(Im::testSet());
        $this->response()->write("\n");
        $this->response()->write(Im::testGet());
    }
}
```

现在可以启动Server了，如果没有任何错误，请使用<a href="http://evalor.cn/websocket.html">WEBSOCKET CLIENT
</a>测试以下内容。  

-   如果发送`{"class": "Test","action": "index"}`返回 `1 这是一个测试` ，则说明Redis连接正常。

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

### 全服务器广播

全服务器广播实际上是给全部fd连接发送消息。

### 房间消息

房间消息其实是指发送信息到具体房间中的一个概念，房间只是fd的一种组织(管理)形式，在房间这个概念中，实际上并不需要uid这个概念，因为你在公会频道收不到队伍消息嘛。

### 回收fd

由于用户断线时，我们只能获取到fd，并不能获取到roomId和userId，所以我们必须设计一套回收机制，保证Redis中的映射关系不错误；防止信息发送给错误的fd。

## 代码实现

**注意：以下代码均是基本逻辑，业务使用需要根据自己业务场景丰富**

### Im基本逻辑

```php
<?php
namespace App\WebSocket\Logic;

use EasySwoole\Core\Component\Di;


class Im
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
     * 设置User => Fd 映射
     * @param int $userId userId
     * @param int $fd     fd
     * @return void
     */
    protected static function setUserFdMap(int $userId, int $fd)
    {
        $fdList = self::findFdListToUserId($userId);
        // 检查此user 是否已经存在fd
        if (is_null($fdList)) {
            $fdList = [];
        }
        array_push($fdList, $fd);
        self::setUserFdList($userId, $fdList);
    }

    /**
     * 设置User Fd list
     * @param int   $userId userId
     * @param array $fdList fd List
     */
    protected static function setUserFdList(int $userId, array $fdList)
    {
        self::getRedis()->hSet('userIdFdMap', $userId, json_encode($fdList));
    }

    /**
     * 通过userId 查询 fd list
     * @param  int    $userId userId
     * @return array|null    此userId 的fdList
     */
    protected static function findFdListToUserId(int $userId)
    {
        return json_decode(self::getRedis()->hGet('userIdFdMap', $userId), true);
    }

    /**
     * 通过Fd 删除UserId => Fd Map
     * @param  int    $fd fd
     * @return void
     */
    protected static function deleteUserIdFdMapByFd(int $fd)
    {
        $userId = self::findUserIdByFd($fd);
        $fdList = self::findFdListToUserId($userId);
        foreach ($fdList as $number => $valFd) {
            if ($valFd == $fd) {
                unset($fdList[$number]);
            }
        }
        self::setUserFdList($userId, $fdList);
    }

    /**
     * 设置Fd => userId 映射
     * @param int $userId userId
     * @param int $fd     fd
     * @return void
     */
    protected static function setFdUserMap(int $userId, int $fd)
    {
        self::getRedis()->hSet('fdUserIdMap', $fd, $userId);
    }

    /**
     * 通过Fd 删除 Fd => UserId Map
     * @param  int    $fd fd
     * @return void
     */
    protected static function deleteFdUserIdMapByFd(int $fd)
    {
        self::getRedis()->hDel('fdUserIdMap', $fd);
    }

    /**
     * 通过fd 查询 userId
     * @param  int    $fd fd
     * @return int     userId
     */
    protected static function findUserIdByFd(int $fd)
    {
        return (int)self::getRedis()->hGet('fdUserIdMap', $fd);
    }

    /**
     * 将fd 推入 room list
     * @param int $roomId roomId
     * @param int $fd     fd
     * @param int $userId userId
     */
    protected static function roomPush(int $roomId, int $fd, int $userId)
    {
        self::getRedis()->hSet("room:{$roomId}", $fd, $userId);
    }

    /**
     * 获取Room 中全部 fd list
     * @param  int $roomId roomId
     * @return array|null         fd list
     */
    protected static function getRoomFdList(int $roomId)
    {
        return self::getRedis()->hKeys("room:{$roomId}");
    }

    /**
     * 获取Room 中的全 userId list
     * @param  int    $roomId roomId
     * @return array|null         userId list
     */
    protected static function getRoomUserIdList(int $roomId)
    {
        return self::getRedis()->hVals("room:{$roomId}");
    }

    /**
     * 删除Room中的Fd
     * @param  int    $fd fd
     * @return void
     */
    protected static function deleteRoomFd(int $roomId, int $fd)
    {
        self::getRedis()->hDel("room:{$roomId}", $fd);
    }

    /**
     * 设置 Fd => RoomId 映射
     * @param int $fd     fd
     * @param int $userId userId
     */
    protected static function setFdRoomIdMap(int $fd, int $roomId)
    {
        self::getRedis()->hSet('roomIdFdMap', $fd, $roomId);
    }

    /**
     * 删除fd 在 RoomId => fd 映射
     * @param  int    $fd fd
     * @return void
     */
    protected static function deleteRoomIdMapByFd(int $fd)
    {
        self::getRedis()->hDel('roomIdFdMap', $fd);
    }

    /**
     * 通过Fd 查询 RoomId
     * @param  int    $fd fd
     * @return int     RoomdId
     */
    protected static function findRoomIdToFd(int $fd)
    {
        return (int)self::getRedis()->hGet('roomIdFdMap', $fd);
    }

    /**
     * 绑定User和fd的关系
     * @param  int    $userId userId
     * @param  int    $fd     fd
     * @return void
     */
    public static function bindUser(int $userId, int $fd)
    {
        self::setFdUserMap($userId, $fd);
        self::setUserFdMap($userId, $fd);
    }

    /**
     * 进入房间
     * @param  int    $roomId roomId
     * @param  int    $fd     fd
     * @return void
     */
    public static function joinRoom(int $roomId, int $fd, int $userId)
    {
        self::roomPush($roomId, $fd, $userId);
        self::setFdRoomIdMap($fd, $roomId);
    }

    /**
     * 获取UserId
     * @param  int    $fd fd
     * @return int  userId
     */
    public static function getUserId(int $fd)
    {
        return self::findUserIdByFd($fd);
    }

    /**
     * 获取User的Fd
     * @param  int    $userId userId
     * @return array         fdList
     */
    public static function getUserFd(int $userId)
    {
        return self::findFdListToUserId($userId);
    }

    /**
     * 获取RoomId
     * @param  int    $fd fd
     * @return int     roomId
     */
    public static function getRoomId(int $fd)
    {
        return self::findRoomIdToFd($fd);
    }

    /**
     * 查询房间内的全部fd
     * @param  int    $roomId roomId
     * @return array|null         fd列表
     */
    public static function selectRoomFd(int $roomId)
    {
        return self::getRoomFdList($roomId);
    }

    /**
     * 查询房间内的全部userId
     * @param  int    $roomId roomId
     * @return array|null $
     */
    public static function selectRoomUserId(int $roomId)
    {
        return self::getRoomUserIdList($roomId);
    }

    /**
     * 退出房间
     * @param  int    $roomId roomId
     * @param  int    $fd      fd
     * @return void
     */
    public static function exitRoom(int $roomId, int $fd)
    {
        self::deleteRoomIdMapByFd($fd);
        self::deleteRoomFd($roomId, $fd);
    }

    /**
     * 回收fd
     * 解除fd的全部关联关系
     * @param  int    $fd fd
     * @return void
     */
    public static function recyclingFd(int $fd)
    {
        // 解除UserId => Fd 关系
        self::deleteUserIdFdMapByFd($fd);
        // 解除Fd => UserId 关系
        self::deleteFdUserIdMapByFd($fd);
        // 解除RoomId => Fd 关系
        self::exitRoom(self::getRoomId($fd), $fd);
    }
}

```

### Test测试用控制器

```php
<?php
namespace App\WebSocket\Controller;

use EasySwoole\Core\Socket\AbstractInterface\WebSocketController;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;

use App\WebSocket\Logic\Im;

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
        $userId = (int)$param['userId'];
        $roomId = (int)$param['roomId'];

        $fd = $this->client()->getFd();
        Im::bindUser($userId, $fd);
        Im::joinRoom($roomId, $fd, $userId);
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
        $roomId = (int)$param['roomId'];

        // 注：单例Redis 可以将获取$list操作放在TaskManager中执行
        // 连接池的Redis 则不可以, 因为默认Task进程没有RedisPool对象。
        $list = Im::selectRoomFd($roomId);
        //异步推送
        TaskManager::async(function ()use($list, $roomId, $message){
            foreach ($list as $fd) {
                ServerManager::getInstance()->getServer()->push((int)$fd, $message);
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
        $userId = (int)$param['userId'];

        // 注：单例Redis 可以将获取$list操作放在TaskManager中执行
        // 连接池的Redis 则不可以, 因为默认Task进程没有RedisPool对象。
        $fdList = Im::getUserFd($userId);
        // 异步推送
        TaskManager::async(function ()use($fdList, $userId, $message){
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
use \App\WebSocket\Parser as WebSocketParser;
// 引入上文Redis连接
use \App\Utility\Redis;
// 引入上文Room文件
use \App\WebSocket\Logic\Im;

// ...省略
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // 注册WebSocket解析器
    EventHelper::registerDefaultOnMessage($register, WebSocket::class);
    //注册onClose事件
    $register->add($register::onClose, function (\swoole_server $server, $fd, $reactorId) {
        //清除Redis fd的全部关联
        Im::recyclingFd($fd);
    });
    // 注册Redis
    Di::getInstance()->set('REDIS', new Redis(Config::getInstance()->getConf('REDIS')));
}
```

现在可以启动Server了，如果没有任何错误，请使用<a href="http://evalor.cn/websocket.html">WEBSOCKET CLIENT
</a>测试以下内容。

- 用多个浏览器标签打开WEBSOCKET CLIENT页面
- 第一个标签开启连接时发送{"class": "Test","action": "intoRoom","data":{"userId":"1","roomId":"1000"}}
- 第二个标签开启连接时发送{"class": "Test","action": "intoRoom","data":{"userId":"2","roomId":"1000"}}
- 发送{"class": "Test","action": "sendToRoom","data":{"roomId":"1000","message":"发送房间消息"}}，此时多个标签连接都会收到该消息
- 第二个标签发送 {"class": "Test","action": "sendToUser","data":{"userId":"1","message":"发送私聊消息"}}，此时第一个标签连接会收到消息

_至此已经完成了Im的基本逻辑，下面将介绍如何实现js消息处理_

## js消息处理

我们可以利用JSON数据来实现js消息解析

示例
```JSON
// 客户端发送JSON消息格式
{
    "class": "Test",   // 请求控制器
    "action": "intoRoom",   // 请求方法
    "data":{    // 请求参数
        "a":"",
        "b":""
    }
}

// 服务端发送JSON消息格式
{
    "code":"200",    // 状态码，用于标记状态
    "msg":"string"   // 信息，用于标记本次状态的描述
    "result":{       // 结构，用于传输实际数据，通常是个多维结构
        "type":"chat||gift||notice"     // 类型，标记本次消息的类型，如聊天、礼物
        "data":"message"                // 数据，用于传输实际内容，如具体的信息
    }
}
```

当客户端收到消息时，使用JSON.parse就可以解析具体的事件。

### 连接池Redis

*这里仅给出示例*

#### 系统运行常量

```php
<?php
namespace App\Utility;

class SysConst
{
    /**
     * redis连接池处理类
     * @var string
     */
    const REDIS_POOL_CLASS = 'App\\Utility\\RedisPool';

}

```
####

```php
<?php
namespace App\Utility;

use EasySwoole\Config;
use EasySwoole\Core\Component\Pool\AbstractInterface\Pool;
use EasySwoole\Core\Swoole\Coroutine\Client\Redis;

class RedisPool extends Pool
{
    /**
     * 实现getObj方法
     * @param  float  $timeOut 超时连接等待时间
     * @return null|Redis          Redis连接对象
     */
    public function getObj($timeOut = 0.1) : ? Redis
    {
        // TODO: Change the autogenerated stub
        return parent::getObj($timeOut);
    }

    /**
     * 实现创建对象方法
     * @return Redis
     */
    protected function createObject()
    {
        $conf = Config::getInstance()->getConf('REDIS');
        $redis = new Redis($conf['host'], $conf['port'], $conf['serialize'], $conf['auth']);
        if (is_callable($conf['errorHandler'])) {
            $redis->setErrorHandler($conf['errorHandler']);
        }
        try {
            $redis->exec('select', $conf['dbName'] ?? 0);
        } catch (\Exception $e) {
        }
        return $redis;
    }
}

```
#### Redis实例

*可以使用__callStatic 来代理全部redis命令 以下仅做示例*

*如果使用下面的Redis类作为Redis对象，请修改上文的Im.php*

```php
/**
 * 获取Redis对象
 * @return object
 */
protected static function getRedis()
{
    // 连接池直接return Redis 即可 不需要获取连接
    return new Redis;
}
```

```php
<?php
namespace App\Utility;

use EasySwoole\Core\Component\Pool\PoolManager;

/**
 * Redis类
 * 在这里实现Redis方法
 */
class Redis
{
    /**
     * Redis连接池对象
     * @var object
     */
    protected static $redisPool;

    /**
     * redis对象
     * @var object
     */
    protected $redis;

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 获取连接池对象
        if (!self::$redisPool instanceof RedisPool) {
            // 静态化的池不会被释放
            self::$redisPool = PoolManager::getInstance()->getPool(SysConst::REDIS_POOL_CLASS);
        }
        $this->redis = self::$redisPool->getObj();
    }

    /**
     * 构析函数
     */
    public function __destruct()
    {
        // 释放连接池对象
        self::$redisPool->freeObj($this->redis);
    }

    /**
     * redis执行代理
     * @param  string $method redis命令
     * @param  mixed  $args   redis参数列表
     * @return string         redis 返回
     */
    public function exec($method, ...$args)
    {
        return $this->redis->exec($method, ...$args);
    }


    public function hSet($key, $field, $value)
    {
        return $this->redis->exec('hSet', $key, $field, $value);
    }

    public function hMset($key, $field, ...$value)
    {
        return $this->redis->exec('hMset', $key, $field, ...$value);
    }

    public function hGet($key, $field)
    {
        return $this->redis->exec('hGet', $key, $field);
    }

    public function hMget($key, $field, ...$value)
    {
        return $this->redis->exec('hMget', $key, $field, ...$value);
    }

    public function hGetAll($key)
    {
        return $this->redis->exec('hGetAll', $key);
    }

    public function hDel($key, ...$field)
    {
        return $this->redis->exec('hDel', $key, ...$field);
    }

    public function hExists($key, $field)
    {
        return $this->redis->exec('hExists', $key, $field);
    }

    public function hKeys($key)
    {
        return $this->redis->exec('hKeys', $key);
    }

    public function hVals($key)
    {
        return $this->redis->exec('hVals', $key);
    }

    public function sAdd($key, ...$member)
    {
        return $this->redis->exec('sAdd', $key, ...$member);
    }

    public function sRem($key, ...$member)
    {
        return $this->redis->exec('sRem', $key, ...$member);
    }

    public function sMembers($key)
    {
        return $this->redis->exec('smembers', $key);
    }

    public function sIsMember($key, $member)
    {
        return $this->redis->exec('sIsMember', $key, $member);
    }
}

```

### Demo项目地址
<a href="https://github.com/RunsTp/easyChat">easyChat
注: 仅做示例
