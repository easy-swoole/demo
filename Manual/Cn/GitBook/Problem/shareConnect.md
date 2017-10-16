# 数据库连接共用问题
必须每个进程单独创建Redis、MySQL、PDO连接，其他的存储客户端同样也是如此。原因是如果共用1个连接，那么返回的结果无法保证被哪个进程处理。持有连接的进程理论上都可以对这个连接进行读写，这样数据就发生错乱了。

> 极端情况下，若只能有一个连接，不得已共用的情况下，建议通过消息队列来解决问题，具体请参考示例代码中的Kafka应用。

## 如何避免这种情况
由于进程克隆原因，因此尽可能避免在Swoole启动前的位置创建数据库链接（回调事件是服务启动会才会被调用的，onStart事件除外）。
### 产生场景

简单的，我们把Swoole类比为pcntl_fork(),那么就有：
```
/*
在pcntl_fork之前发生进程克隆的链接，在执行了pcntl_fork之后，会在A、B两区域共享。
就好比在Swoole Server启动前去创建链接。
*/
$con = new mysqli();//创建一个数据库链接
//pcntl_fork就好比执行了Server->start();会发生进程克隆。
if(pcntl_fork()){
    //A
    $con->exec($sql1);
}else{
    //B
    $con->exec($sql2);
}
```
由于进程调度，导致不同进程分配到的CPU时间是零散，不连续的，因此会导致A、B两区域的代码块执行时间是错杂的，
最后执行顺序就是可能是：
- A进程写了sql1前一半的数据进去
- B进程写了sql2前一半的数据进去
- A进程继续写了sql1后一半的数据进去
- B进程继续写了sql2后一半的数据进去

因此，这样就会导致执行出问题。所以应该是：
```
if(pcntl_fork()){
    //A
    $con = new mysqli();//创建一个数据库链接
    $con->exec($sql1);
}else{
    //B
    $con = new mysqli();//创建一个数据库链接
    $con->exec($sql2);
}
```
类比Swoole，A、B代码区域就好比Swoole的各个回调事件（onStart事件除外），执行这些回调事件的时候，已经发生了进程克隆。

### 类比EasySwoole
EasySwoole中，frameInitialize、frameInitialized、beforeWorkerStart、onStart均属于服务启动前事件。而IOC容器，也就是Di，是借助单例模式实现的，在服务启动后，会由于进程克隆原因，Di在服务启动前所
注入的数据会在各个进程中复制一份，并独立执行保存进程启动以后的状态数据。如何正确使用IOC来管理单例链接：
```
$conf = Config::getInstance()->getConf('MYSQL');
Di::getInstance()->set(SysConst::MYSQL,\MysqliDb::class,array(
   'host' => $conf['HOST'],
   'username' => $conf['USER'],
   'password' => $conf['PASSWORD'],
   'db' => $conf['DB_NAME'],
   'port' => $conf['PORT'],
));
```
在frameInitialize、frameInitialized、beforeWorkerStart三个事件中，均可做IOC注入。
> 由于EasySwoole中的Di是懒惰加载模式，因此注入到Di中的数据，此刻实际上只有类名，和实例化参数。但是当执行了$di->get('MYSQL')后，Di中MYSQL这个键值将会变为实例化后的MYSQL对象。
因此请避免在服务启动前的事件中调用$di->get('MYSQL')。


错误用法：
```
Di::getInstance()->set('MYSQL',new MysqlDb($conf));
```
> 以上设置到Di的内容就是一个实例化后的数据库对象，因此进程克隆后，仅会有一个连接。


注意，在beforeWorkerStart中去注册swoole的其他回调事件，并在事件回调内使用了$di->get('MYSQL')不算违规调用。若在服务启动前事件需要用到数据库，请单独创建，并勿设置到Di中使用,例如：
```
frameInitialize(){
    $db = new Mysql();
    $db->exec($sql);
    $db->close();
}
```


<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>

