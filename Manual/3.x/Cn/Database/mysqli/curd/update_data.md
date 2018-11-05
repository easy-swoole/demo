## 更新数据
更新数据使用update方法

### update($tableName, $tableData, $numRows = null)
示例:
```php
<?php
$table_name = 'xsk_test';
$result = $db->where('id',1)->update($table_name,['name'=>'231']);
$sql = $db->getLastQuery();
var_dump($result,$sql);
```
生成的sql语句为:
```sql
UPDATE xsk_test SET `name` = '231' WHERE  id = '1'
```

>注意,更新时需要注意是否存在where条件和更新条数,避免造成全表更新

### setValue($tableName, $filedName, $value)
使用setValue可快速更新某个字段的值