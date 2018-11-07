## 连贯操作
mysqli 组件提供了一系列的连贯操作(链式操作)方法,可以有效的提高数据存取的代码清晰度和开发效率,并且支持所有的CURD操作.

### where()
新增一个条件,示例:
```php
<?php
$table_name = 'xsk_test';
$db ->where('name',666,'=','and')
    ->where('id',1,'>','or')
    ->where('id',10,'<','and');
$data = $db->get($table_name);
$sql = $db->getLastQuery();
```
生成的sql语句为:
```sql
SELECT  * FROM xsk_test WHERE  name = '666'  or id > '1'  and id < '10';
```
###orWhere()
等同于where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'OR');
###orderBy()
order方法属于模型的连贯操作方法之一,用于对操作的结果排序.示例:
```php
<?php
$table_name = 'xsk_test';
$db->orderBy('id','desc');
$db->orderBy('code','asc');
$data = $db->get($table_name);
$sql = $db->getLastQuery();
var_dump($data,$sql);
```
生成的sql语句为:
```sql
SELECT  * FROM xsk_test ORDER BY id DESC, code ASC;
```

###groupBy()
GROUP方法也是连贯操作方法之一,通常用于结合合计函数,根据一个或多个列对结果集进行分组.示例:
```php
<?php
$table_name = 'xsk_test';
$db->groupBy('name,code');
$data = $db->get($table_name);
$sql = $db->getLastQuery();
var_dump($data,$sql);
```
生成的sql语句为:
```sql
SELECT  * FROM xsk_test GROUP BY name,code;
```

### join()联表  
用于联表查询以及联表更新等
联表查询示例:
```php
<?php
$table_name = '`xsk_test` as b';
$data = $db->join('`xsk_test_b` as a','a.id = b.id')->get($table_name,null,'*');
$sql = $db->getLastQuery();
var_dump($data,$sql);
```
生成的sql语句为:
```sql
SELECT  * FROM `xsk_test` as b  JOIN `xsk_test_b` as a on a.id = b.id;
```

