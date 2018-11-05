## 其他
mysqli组件还提供了其他方法:

####  resetDbStatus()
重置连接状态,将所有查询缓存(where条件,执行语句等)清空,可用在连接池回收对象上,当你不清楚这个连接是否有缓存时,也可调用该方法重置

####  tableExists($tables)
判断表是否存在(可传数组)
####  inc($num)
更新字段时,实现字段=字段+$num
示例:
```php
<?php
$table_name = 'xsk_test';
$db->update($table_name, ['num'=>$db->inc(3)]);
$sql = $db->getLastQuery();
var_dump($sql);
```
生成的sql语句为:
```sql
UPDATE xsk_test SET `num` = num+3;
```
####  dec()
更新字段时,实现字段=字段-$num
####  setInc()
```
setInc($tableName, $filedName, $num = 1)  
```
 直接自增某个字段
####  setDec()
```
setDec($tableName, $filedName, $num = 1)  
```
直接自减某个字段
####  withTotalCount()
将查询条件的总条数缓存,缓存数据将被getTotalCount()方法调用
####  getTotalCount()
取出withTotalCount的总条数
####  getInsertId()
获取最后插入的id
####  getLastQuery()
获取最后自行的sql语句
####  getLastError()
获取最后一次查询错误的内容
####  getLastErrno()
获取最后一次查询错误的编号