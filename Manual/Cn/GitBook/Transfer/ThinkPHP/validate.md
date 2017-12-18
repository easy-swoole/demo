# 验证器类

------

> Github : [ThinkValidate](https://github.com/top-think/think-validate) - 从ThinkPHP5.1独立出来的验证器类库

安装
------

```bash
composer require topthink/think-validate
```

直接在控制器中使用
------

验证器没有额外的配置文件，可以直接现场定义规则，对数据进行验证，用法与`ThinkPHP`的控制器验证是一致的

```php
use think\Validate;

$validate = Validate::make([
    'name'  => 'require|max:25',
    'email' => 'email'
]);

$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com'
];

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

使用预定义验证器文件
------

如果原项目已经有验证器，可以直接复制过来，修改对应的命名空间即可直接使用

```php
namespace \App\Validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];
    
    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',    
    ];
    
}
```

验证器的调用代码如下

```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \App\Validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

更多验证器用法可以参考5.1完全开发手册的[验证](https://www.kancloud.cn/manual/thinkphp5_1/354101)章节