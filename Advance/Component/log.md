# 日志
easySwoole提供了分类日志服务，以供记录运行信息方便调试。
```
$log = Logger::getInstance();
$log2 = Logger::getInstance('logcategory1');
```
## log
```
$log->log('message1');
$log2->log('message2');
```
## console
```
$log->console("message",false);
```
