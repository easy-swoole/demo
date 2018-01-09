内存管理机制
------

`easySwoole`启动后内存管理的底层原理与普通`PHP-CLI`程序一致，与平时常用的`FastCGI`模式的内存管理略有区别

> 内存泄漏（Memory Leak）是指程序中己动态分配的堆内存由于某种原因程序未释放或无法释放，内存占用逐步增加，严重的时候耗尽系统的所有内存导致程序崩溃

#### 局部变量
------

在事件回调函数返回后，所有局部对象和变量会全部回收，不需要`unset`。如果变量是一个资源类型，那么对应的资源也会被PHP底层释放

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

- $a, $b, $c 都是局部变量，当此函数return时，这3个变量会立即释放，对应的内存会理解释放，打开的IO资源文件句柄会立即关闭。
- $d 也是局部变量，但是return前将它保存到了全局变量$e，所以不会释放。当执行unset($e['client'])时，并且没有任何其他PHP变量仍然在引用$d变量，那么$d 就会被释放。

#### 全局变量
------

在PHP中，有3类全局变量。

- 使用global关键词声明的变量
- 使用static关键词声明的类静态变量、函数静态变量
- PHP的超全局变量，包括`$_GET`、`$_POST`、`$GLOBALS`等

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

#### 解决方法
------

- 同步阻塞并且请求响应式无状态的Server程序可以设置max_request，当Worker进程/Task进程结束运行时或达到任务上限后进程自动退出。该进程的所有变量/对象/资源均会被释放回收。

- 程序内在onClose或设置定时器及时使用unset清理变量，回收资源