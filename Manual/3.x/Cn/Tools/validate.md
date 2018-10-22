## Validate

#### 命名空间地址

EasySwoole\Validate\Validate

#### 方法列表

获取Error：

```php
function getError():?EasySwoole\Validate\Error
```

给字段添加规则：

- string `name`         字段key
- string `errorMsg`     错误信息
    - string `alias`    别名

```php
public function addColumn(string $name,?string $errorMsg = null,?string $alias = null):EasySwoole\Validate\Rule
```

返回一个Rule对象可以添加自定义规则。

数据验证：

- array `data` 数据

```php
function validate(array $data)
```

#### 例子

```php

<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-11
 * Time: 上午10:26
 */

require_once 'vendor/autoload.php';

$data = [
    'name' => 'blank',
    'age'  => 25
];

$valitor = new \EasySwoole\Validate\Validate();
$valitor->addColumn('name', '名字不为空')->required('名字不为空')->lengthMin(10,'最小长度不小于10位');
$bool = $valitor->validate($data);
var_dump($valitor->getError()->getErrorRuleMsg()?:$valitor->getError()->getColumnErrorMsg());

/* 结果：
 string(26) "最小长度不小于10位"
*/
```
