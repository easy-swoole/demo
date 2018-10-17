## Validate

#### 命名空间地址

EasySwoole\Validate\Validate

#### 方法列表

获取Error：

```php
function getError():?EasySwoole\Validate\Error
```

给字段添加规则：

- string `url` 请求地址

```php
public function addColumn(string $name,?string $errorMsg = null,?string $alias = null):EasySwoole\Validate\Rule
```

数据验证：

- array `data` 数据

```php
function validate(array $data)
```

添加POST参数：

- EasySwoole\Curl\Field `field`
- bool `isFile ` 是否为文件

```php
public function addPost(Field $field,$isFile = false):EasySwoole\Curl\Request
```