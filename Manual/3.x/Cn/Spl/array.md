## SplArray使用

SplArray 支持链式操作，如：$array->unique()->asort()->keys();

命名空间地址：

```php
use \EasySwoole\Spl\SplArray;
```

获得默认数组格式：

```php
function getArrayCopy(): array
```

设置数组中元素：

```php
function set($path, $value): void
```

> 如：$config->set('database.host','127.0.0.1');

获得值：

```php
function get($path)
```

```php
$splArray = new SplArray([
    'config' => [
        'mysql' => [
            'name' => 'xxxx',
            'host' => 'xxxx',
        ],
        'php' => [
            'name' => 'xxxx',
            'host' => 'xxxx',
        ]
    ]
]);
var_dump($splArray->get('config.mysql'));
/*
[
  'name' => 'xxxx',
  'host' => 'xxxx',
]
*/
```

删除元素：

```php
function delete($key): void
```

```php
$splArray = new SplArray([
    'config' => [
        'mysql' => [
            'name' => 'xxxx',
            'host' => 'xxxx',
        ],
        'php' => [
            'name' => 'xxxx',
            'host' => 'xxxx',
        ]
    ],
    'other' => ['i m other']
]);
$splArray->delete('config')
var_dump($splArray);
/*
[ 'other' => ['i m other'] ]
*/
```

数组去重取唯一的值：

```php
function unique(): SplArray
```

获取数组中重复的值：
```php
function multiple(): SplArray
```

按照键值升序：
```php
function asort(): SplArray
```

按照键升序：
```php
function ksort(): SplArray
```

自定义排序：
```php
function sort($sort_flags = SORT_REGULAR): SplArray
```

取得某一列：
```php
function column($column, $index_key = null): SplArray
```

交换数组中的键和值：
```php
function flip(): SplArray
```

过滤本数组：
```php
function filter($keys, $exclude = false): SplArray
```

提取数组中的键：
```php
function keys(): SplArray
```

提取数组中的值：
```php
function values(): SplArray
```

清空：
```php
function flush():SplArray
```

