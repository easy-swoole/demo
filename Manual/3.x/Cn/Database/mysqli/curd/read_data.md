## 数据读取
数据读取分为2种读取:读取多条数据,读取单条数据,数据读取支持where等连贯操作,具体连贯操作可查看[连贯操作](../continuous_operation.md),本文不再说明

### getOne($tableName, $columns = '*')
使用getOne方法可读取单条数据
用法:
```php
<?php
$table_name = 'xsk_test';
$data = $db->getOne($table_name,'name,code');
```
生成的sql语句为:
```sql
SELECT  name,code FROM xsk_test LIMIT 1
```

### get($tableName, $numRows = null, $columns = '*')
#### 使用get方法读取多条数据  
用法:
```php
<?php
$table_name = 'xsk_test';
$data = $db->get($table_name,null,'*');
$sql = $db->getLastQuery();
var_dump($data,$sql);
```
生成的sql语句为:
```sql
SELECT  * FROM xsk_test;
```
#### 实现分页:
```php
<?php
$table_name = 'xsk_test';
$page=3;
$page_size=20;
$data = $db->get($table_name,[($page-1)*$page_size,$page_size],'*');
$sql = $db->getLastQuery();
var_dump($data,$sql);
```
生成的sql语句为:
```sql
SELECT  * FROM xsk_test LIMIT 40, 20;
```

### getValue($tableName, $column, $limit = 1)
使用getValue()获取某个字段的值

### getColumn($tableName, $column, $limit = 1)
使用getColumn()获取某一列的数据

### has($tableName)
判断该查询条件下是否存在数据

### 聚合查询方法
  - count()
  - max()
  - min()
  - sum()
  - avg()


