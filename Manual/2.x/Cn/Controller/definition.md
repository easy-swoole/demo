# 控制器定义

控制器名称空间前缀统一为 `{$APPLICATION_DIR}\Controller`，系统默认的应用命名空间为`App`，默认的控制器路径应位于`App/Controller`目录下，类名与文件名保持大小写一致，并且采用大驼峰法命名（驼峰并首字母大写），所有的控制器都应继承`EasySwoole\Core\Http\AbstractInterface\Controller`类，并且实现`index`方法

一个典型的控制器定义如下

```php
<?php
namespace App\Controller;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
    }
}
```

在没有定义路由的情况下，这个控制器的访问路径应该是

```
http://127.0.0.1:9501/index/index
```

理论上控制器可以支持无限层级的嵌套，即`Controller`文件夹可以进行无限层级的嵌套，并通过`PATHINFO`模式的URL访问到，详见[ URL解析规则 ](Structure/dispatch.md)章节

> **danger**
>
> 在控制器中向浏览器输出，应使用框架提供的 **Response** 类，直接 **echo** 或者 **var_dump** 都会输出到启动 easySwoole 框架的命令行控制台中，在控制器方法中 **return** 任何值都不会被输出到控制台或浏览器，也就是说控制器不会主动地去转换 **return** 的结果并输出，这点是需要注意的

