# 数据库断线问题
## MySQL长连接
MySQL短连接每次请求操作数据库都需要建立与MySQL服务器建立TCP连接，这是需要时间开销的。TCP连接需要3次网络通信。这样就增加了一定的延时和额外的IO消耗。请求结束后会关闭MySQL连接，还会发生3/4次网络通信。

> close操作不会增加响应延时，原因是close后是由操作系统自动进行通信的，应用程序感知不到

长连接就可以避免每次请求都创建连接的开销，节省了时间和IO消耗。提升了PHP程序的性能。

## 断线重连
在cli环境下，PHP程序需要长时间运行，客户端与MySQL服务器之间的TCP连接是不稳定的。

- MySQL-Server会在一定时间内自动切断连接
- PHP程序遇到空闲期时长时间没有MySQL查询，MySQL-Server也会切断连接回收资源
- 其他情况，在MySQL服务器中执行kill process杀掉某个连接，MySQL服务器重启

这时PHP程序中的MySQL连接就失效了。如果仍然执行mysql_query，就会报一个“MySQL server has gone away”的错误。程序处理不到就直接遇到致命错误并退出了。所以PHP程序中需要断线重连。

有很多人提出了mysql_ping的方案，每次mysql_query进行连接检测或者定时连接检测。这个方案不是最好的。原因是

- mysql_ping需要主动侦测连接，带来了额外的消耗
- 定时执行mysql_ping不能解决问题，如刚刚执行过mysql_ping检测之后，连接就关闭了

最佳的方案是，进行断线重连 。它的原理是：

- mysql_query执行后检测返回值
- 如果mysql_query返回失败，检测错误码发现为2006/2013（这2个错误表示连接失败），再执行一次mysql_connect
- 执行mysql_connect后，重新执行mysql_query，这时必然会成功，因为已经重新建立了连接
- 如果mysql_query返回成功，那么连接是有效的，这是一次正常的调用

## 示例代码

以Mysqli数据类库为例[https://github.com/joshcam/PHP-MySQLi-Database-Class](https://github.com/joshcam/PHP-MySQLi-Database-Class)
在MysqliDb.php文件中的_prepareQuery方法可见以下代码：
```
$stmt = $this->mysqli()->prepare($this->_query);
if ($stmt !== false) {
     if ($this->traceEnabled)
           $this->traceStartQ = microtime(true);
     return $stmt;
}
if ($this->mysqli()->errno === 2006 && $this->autoReconnect === true && $this->autoReconnectCount === 0) {
      $this->connect($this->defConnectionName);
      $this->autoReconnectCount++;
      return $this->_prepareQuery();
}
$error = $this->mysqli()->error;
$query = $this->_query;
$errno = $this->mysqli()->errno;

```