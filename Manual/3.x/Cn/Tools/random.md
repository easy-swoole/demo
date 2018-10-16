## Random

#### 命名空间地址

EasySwoole\Utility\Random

#### 方法列表

字符串随机生成：

- int    `length`     生成长度
- string `alphabet`   自定义生成字符集

```php
static function character($length = 6, $alphabet = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789')
```

纯数字字符串随机生成：

- int `length` 生成长度

```php
static function number(length = 6)
```

