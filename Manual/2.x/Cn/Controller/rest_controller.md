# REST控制器

easySwoole 支持REST风格开发，在实现上，其实是对基础控制器进行了REST规则封装，本质上，也是一个控制器， 支持GET、POST、PUT、PATCH、DELETE、HEAD、OPTIONS等方法



## 实例代码

> **info no-icon**
>
> 注意 action 的命名规则为 "请求方法(全大写) + 实际方法名(大驼峰)" 如下面的例子

```php
<?php

namespace App\Controller;

use EasySwoole\Core\Http\AbstractInterface\REST;

class Index extends REST
{
    function GETIndex()
    {
        // GET请求访问 index 方法
    }

    function POSTIndex()
    {
        // POST请求访问 index 方法
    }

    function GETTest()
    {
        // GET请求访问 test 方法
    }

    function POSTTest()
    {
        // POST请求访问 test 方法
    }
}
```

