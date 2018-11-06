## EasySwoole-Mysqli
EasySwoole提供了mysqli的组件,github地址:https://github.com/easy-swoole/mysqli
该组件是基于mysqli-db更改为swoole的异步mysql扩展封装的数据库操作类

### 说明
由于es3.x版本是全协程版本,无法直接使用think-orm,laravel-orm等单例数据库orm,所以EasySwoole提供了mysqli组件用于数据库操作