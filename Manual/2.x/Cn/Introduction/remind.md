# 开发者必读

## Swoole版本选择
EasySwoole 2.x版本全面支持swoole1.x与swoole2.x。若有协程需求，请使用swoole2.x。但对于大部分新手而言，若没有协程概念（可以理解为进程内多线程），
推荐还是配合swoole1.x使用，因为很多场景下，static变量在协程下是非常不安全的，例如单例模式的数据库连接。
若生产环境想上swoole，如果没有太多的基础或者是对内核执行机制比较了解的同学，推荐使用swoole1.x

> 并不是swoole1.x就比swoole2.x差或者老，其实只是两个不同分支

## 注意事项
- 不要在代码中执行sleep以及其他睡眠函数，这样会导致整个进程阻塞
    exit/die是危险的，会导致worker进程退出
- 可通过register_shutdown_function来捕获致命错误，在进程异常退出时做一些请求工作。
- PHP代码中如果有异常抛出，必须在回调函数中进行try/catch捕获异常，否则会导致工作进程退出
- swoole不支持set_exception_handler，必须使用try/catch方式处理异常
- Worker进程不得共用同一个Redis或MySQL等网络服务客户端，Redis/MySQL创建连接的相关代码可以放到onWorkerStart回调函数中。

## 类/函数重复定义

- 新手非常容易犯这个错误，由于easySwoole是常驻内存的，所以加载类/函数定义的文件后不会释放。因此引入类/函数的php文件时必须要使用include_once或require_once，否会发生cannot redeclare function/class 的致命错误。


## 进程隔离与内存管理

- 进程隔离也是很多新手经常遇到的问题。修改了全局变量的值，为什么不生效，原因就是全局变量在不同的进程，内存空间是隔离的，所以无效。
所以使用easySwoole开发Server程序需要了解进程隔离问题。不同的进程中PHP变量不是共享，即使是全局变量，在A进程内修改了它的值，在B进程内是无效的，如果需要在不同的Worker进程内共享数据，可以用Redis、MySQL、文件、Swoole\Table、APCu、shmget等工具实现
还有，不同进程的文件句柄是隔离的，所以在A进程创建的Socket连接或打开的文件，在B进程内是无效，即使是将它的fd发送到B进程也是不可用的。

- 进程克隆。在Server启动时，主进程会克隆当前进程状态，此后开始进程内数据相互独立，互不影响。有疑问的新手可以先弄懂php的pcntl

### swoole_server中对象的4层生命周期

开发swoole程序与普通LAMP下编程有本质区别。在传统的Web编程中，PHP程序员只需要关注request到达，request结束即可。而在swoole程序中程序员可以操控更大范围，变量/对象可以有四种生存周期。

    变量、对象、资源、require/include的文件等下面统称为对象

#### 程序全局期

在swoole_server->start之前就创建好的对象，我们称之为程序全局生命周期。这些变量在程序启动后就会一直存在，直到整个程序结束运行才会销毁。

有一些服务器程序可能会连续运行数月甚至数年才会关闭/重启，那么程序全局期的对象在这段时间持续驻留在内存中的。程序全局对象所占用的内存是Worker进程间共享的，不会额外占用内存。

这部分内存会在写时分离（COW），在Worker进程内对这些对象进行写操作时，会自动从共享内存中分离，变为进程全局对象。

    程序全局期include/require的代码，必须在整个程序shutdown时才会释放，reload无效

#### 进程全局期

swoole拥有进程生命周期控制的机制，一个Worker子进程处理的请求数超过max_request配置后，就会自动销毁。Worker进程启动后创建的对象（onWorkerStart中创建的对象），在这个子进程存活周期之内，是常驻内存的。onConnect/onReceive/onClose 中都可以去访问它。

    进程全局对象所占用的内存是在当前子进程内存堆的，并非共享内存。对此对象的修改仅在当前Worker进程中有效
    进程期include/require的文件，在reload后就会重新加载

#### 会话期

会话期是在onConnect后创建，或者在第一次onReceive时创建，onClose时销毁。一个客户端连接进入后，创建的对象会常驻内存，直到此客户端离开才会销毁。

在LAMP中，一个客户端浏览器访问多次网站，就可以理解为会话期。但传统PHP程序，并不能感知到。只有单次访问时使用session_start，访问$_SESSION全局变量才能得到会话期的一些信息。

swoole中会话期的对象直接是常驻内存，不需要session_start之类操作。可以直接访问对象，并执行对象的方法。
#### 请求期

请求期就是指一个完整的请求发来，也就是onReceive收到请求开始处理，直到返回结果发送response。这个周期所创建的对象，会在请求完成后销毁。

swoole中请求期对象与普通PHP程序中的对象就是一样的。请求到来时创建，请求结束后销毁。


#### swoole_server中内存管理机制

swoole_server启动后内存管理的底层原理与普通php-cli程序一致。具体请参考Zend VM内存管理方面的文章。

#### 局部变量

在事件回调函数返回后，所有局部对象和变量会全部回收，不需要unset。如果变量是一个资源类型，那么对应的资源也会被PHP底层释放。

```
function test()
{
    $a = new Object;
    $b = fopen('/data/t.log', 'r+');
    $c = new swoole_client(SWOOLE_SYNC);
    $d = new swoole_client(SWOOLE_SYNC);
    global $e;
    $e['client'] = $d;
}

```
$a, $b, $c 都是局部变量，当此函数return时，这3个变量会立即释放，对应的内存会立即释放，打开的IO资源文件句柄会立即关闭。
$d 也是局部变量，但是return前将它保存到了全局变量$e，所以不会释放。当执行unset($e['client'])时，并且没有任何其他PHP变量仍然在引用$d变量，那么$d 就会被释放。

#### 全局变量

在PHP中，有3类全局变量。

- 使用global关键词声明的变量
- 使用static关键词声明的类静态变量、函数静态变量
- PHP的超全局变量，包括$_GET、$_POST、$GLOBALS等

全局变量和对象，类静态变量，保存在swoole_server对象上的变量不会被释放。需要程序员自行处理这些变量和对象的销毁工作。

```
class Test
{
    static $array = array();
    static $string = '';
}

function onReceive($serv, $fd, $reactorId, $data)
{
    Test::$array[] = $fd;
    Test::$string .= $data;
}
```

- 在事件回调函数中需要特别注意非局部变量的array类型值，某些操作如 TestClass::$array[] = "string" 可能会造成内存泄漏，严重时可能发生爆内存，必要时应当注意清理大数组。
- 在事件回调函数中，非局部变量的字符串进行拼接操作是必须小心内存泄漏，如 TestClass::$string .= $data，可能会有内存泄漏，严重时可能发生爆内存。

解决方法
- 同步阻塞并且请求响应式无状态的Server程序可以设置max_request，当Worker进程/Task进程结束运行时或达到任务上限后进程自动退出。该进程的所有变量/对象/资源均会被释放回收。
- 程序内在onClose或设置定时器及时使用unset清理变量，回收资源

#### 异步客户端

Swoole提供的异步客户端与普通的PHP变量不同，异步客户端在发起connect时底层会增加一次引用计数，在连接close时会减少引用计数。
> 包括swoole_client、swoole_mysql、swoole_redis、swoole_http_client

```
function test()
{
    $client = new swoole_client(SWOOLE_TCP | SWOOLE_ASYNC);
    $client->on("connect", function($cli) {
        $cli->send("hello world\n");
    });
    $client->on("receive", function($cli, $data){
        echo "Received: ".$data."\n";
        $cli->close();
    });
    $client->on("error", function($cli){
        echo "Connect failed\n";
    });
    $client->on("close", function($cli){
        echo "Connection close\n";
    });
    $client->connect('127.0.0.1', 9501);
    return;
}
```

$client是局部变量，常规情况下return时会销毁。但这个$client是异步客户端在执行connect时swoole引擎底层会增加一次引用计数，因此return时并不会销毁。
该客户端执行onReceive回调函数时进行了close或者服务器端主动关闭连接触发onClose，这时底层会减少引用计数，$client才会被销毁。

> 内存管理部分参照了swoole官方文档。

## 约定规范

- 项目中类名称与类文件(文件夹)命名，均为大驼峰，变量与类方法为小驼峰。
- 在HTTP响应中，于业务逻辑代码中echo $var 并不会将$var内容输出至相应内容中，请调用Response实例中的wirte()方法实现。
