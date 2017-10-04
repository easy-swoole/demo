# ShareMemory
ShareMemory是为了解决多进程下进程数据隔离的问题。基于文件+锁的形式来实现。

## 方法列表
### getInstance
用于获取一个ShareMemory实例。
```
use \Core\Component\ShareMemory;
$share = ShareMemory::getInstance();
//或者是
$share = ShareMemory::getInstance(ShareMemory::SERIALIZE_TYPE_SERIALIZE);
```

> 对数据的存储，EasySwoole默认选用json_encode的方式，若需要存储对象，请选SERIALIZE_TYPE_SERIALIZE。

### set && get
```
$share->set("a",2);
$share->set('a',$share->get("a"));
var_dump($share->get("a"));
$share->set("b.c","aaaa");
echo $share->get("b.c");
```
### startTransaction && commit && rollback
```
$share = \Core\Component\ShareMemory::getInstance(\Core\Component\ShareMemory::SERIALIZE_TYPE_SERIALIZE);
$share->startTransaction();
$share->set("a",2);
$share->set('a',$share->get("a"));
var_dump($share->get("a"));
$share->commit();
var_dump($share->get("a"));
$share->set("b.c","aaaa");
echo $share->get("b.c");
```
> 事务必须提交，否则会引起死锁。

```
$share->startTransaction();
$share->set("a",2);
$share->set('a',$share->get("a"));
$share->rollback();
var_dump($share->get("a"));
$share->commit();
var_dump($share->get("a"));

$share->set("b.c","aaaa");
echo $share->get("b.c");

```

### clear
```
$share->clear();
```
> 注意，该操作会清除全部的数据。