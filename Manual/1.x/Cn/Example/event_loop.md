# 自定义Event Loop

EasySwoole支持自定义添加一个socket资源参与系统底层的事件调度循环，添加事件循环与swoole原生的[EventLoop](https://wiki.swoole.com/wiki/page/242.html)一致，这里只做简单介绍，扩展应用请参照swoole文档

```
// 函数原型
bool swoole_event_add(int $sock, mixed $read_callback, mixed $write_callback = null, int $flags = null);
```

> 在 Server 程序中使用时，必须在 Worker 进程启动后使用。在 Server::start 之前不得调用任何异步 IO 接口

参数1($sock)可以为以下三种类型：

- int，就是文件描述符,包括swoole_client的socket,以及第三方扩展的socket（比如mysql）
- stream资源，就是stream_socket_client/fsockopen 创建的资源
- sockets资源，就是sockets扩展中 socket_create创建的资源，需要在编译时加入 ./configure --enable-sockets

参数2为可读回调函数，参数3为可写事件回调，可以是字符串函数名、对象+方法、类静态方法或匿名函数，当此socket可读时回调指定的函数。

参数4为事件类型的掩码，可选择关闭/开启可读可写事件，如`SWOOLE_EVENT_READ`，`SWOOLE_EVENT_WRITE`，或者`SWOOLE_EVENT_READ` | `SWOOLE_EVENT_WRITE`

回调函数
------
- 在可读事件回调函数中必须使用`fread`、`recv`等函数读取Socket缓存区中的数据，否则事件会持续触发，如果不希望继续读取必须使用`Swoole\Event::del`移除事件监听
- 在可写事件回调函数中，写入socket之后必须调用`Swoole\Event::del`移除事件监听，否则可写事件会持续触发
- 执行`fread`、`socekt_recv`、`socket_read`、`Swoole\Client::recv`返回false，并且错误码为`EAGAIN`时表示当前Socket接收缓存区内没有任何数据，这时需要加入可读监听等待EventLoop通知
- 执行`fwrite`、`socket_write`、`socket_send`、`Swoole\Client::send`操作返回false，并且错误码为`EAGAIN`时表示当前Socket发送缓存区已满，暂时不能发送数据。需要监听可写事件等待EventLoop通知

例子
------

例如在Conf/Event.php中的`onWorkerStart`事件中，添加以下代码:

```
if($workerId == 0){
    $listener = stream_socket_server(
        "udp://0.0.0.0:9504",
        $error,
        $errMsg,
        STREAM_SERVER_BIND
    );
    if ($errMsg) {
        throw new \Exception("cluster server bind error on msg :{$errMsg}");
    } else {
        //加入event loop
        swoole_event_add($listener, function ($listener) {
            $data = stream_socket_recvfrom($listener, 9504, 0, $client);
            var_dump($data);
            stream_socket_sendto($listener, "hello", 0, $client());
        }
        );
    }
}
```

启动EasySwoole，执行以下UDP客户端测试代码
```
$client = new swoole_client(SWOOLE_SOCK_UDP);
if (!$client->connect('127.0.0.1', 9504, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("hello\n");
echo $client->recv();
$client->close();
```

> 当客户端发送给服务端消息时，则会自动调用swoole_event_add中所注册的事件回调逻辑。

