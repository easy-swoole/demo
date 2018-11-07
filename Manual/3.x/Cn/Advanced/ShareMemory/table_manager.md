# TableManager
EasySwoole对Swoole table进行了基础的封装。

## 方法列表

### getInstance()
该方法用于获取TableManager管理器实例

### add($name,array $columns,$size = 1024)
该方法用于创建一个table

### get($name):?Table
该方法用于获取已经创建好的table

## 示例代码

```php
TableManager::getInstance()->add(
    self::TABLE_NAME,
    [
        'currentNum'=>['type'=>Table::TYPE_INT,'size'=>2],
    ],
    1024
);
```

> 注意事项：请勿在onRequest、OnReceive等回调位置创建swoole table,swoole table应该在服务启动前创建，比如在mainServerCreate事件中创建。