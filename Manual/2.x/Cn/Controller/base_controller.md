# 基础控制器

> EasySwoole\Core\Http\AbstractInterface\Controller

当一个 WEB 请求抵达 Swoole Server 后，经过框架的分发，首先到达的便是控制器的逻辑，在这里可以编写业务逻辑，对请求进行处理，一个最小化的基础控制器定义代码如下

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

## 抽象方法

用于处理`WEB`请求的控制器都需要继承本类，并且实现`index`抽象方法

`index`

控制器的默认处理方法，当 URL 中无法解析出 actionName 时，将默认执行该方法。例如有一个Test控制器，当访问domain/test路径时，则默认解析为index

## 实体方法

`onRequest`

当一个URL请求进来，能够被映射到控制器且做完 actionName 解析后，将立马执行 OnRequest 方法，并获取返回结果，如果本方法返回 false 则拦截该请求，不继续往下处理，本方法可以对请求做预处理，如权限过滤等

> **info**
>
> 需要注意本方法和全局事件注册的 OnRequest 方法并不冲突，全局事件的优先级最高

`actionNotFound`

当在URL中解析出 actionName ，而在控制器中无存在对应方法（函数）时，则执行该方法，例如有一个 Test 控制器，当访问 domain/test/test1/index.html 路径时，actionName 会被解析为 test1，而此时若控制器中无test1方法时，则执行本方法中的逻辑

`afterAction`

在任何的控制器响应结束后，均会执行该方法，可以用于日志记录，清理等各种善后工作

`onException`

在控制器中发生任何异常，都会触发本方法，可以拦截到`Throwable`类型的所有异常，用于对逻辑中的异常进行统一拦截处理，在没有覆盖本方法的时候，默认会将发生的异常继续向上抛出

`resetAction`

在 onRequest 方法中调用本方法，可以对本次请求调用的方法进行 "跳转" 操作，将当前的请求重新定位到本类的另一个方法

`validateParams`

快速调用框架自带的验证器进行请求参数验证，如果不想用框架自带验证器，可以新建Base控制器继承并重写方法

`getActionName`

当一个 URL 请求进来，能够被映射到控制器时，那么将从该 URL 中解析出对应的行为名称，若无则默认为index，在控制器内的任意位置调用 $this->getActionName() 均能获得当前行为名称

`request`

获取当前请求的 EasySwoole\Core\Http\Request 实例

`response`

获取当前请求的 EasySwoole\Core\Http\Response 实例

`__hook`

当一个请求进来，能解析出控制器时，会立马执行控制器的 __hook 方法，并将本次请求的Response、Request实例以及请求的方法名称一并传入，hook 方法可以控制这个请求的全部逻辑，具体请直接查看源码

> **danger**
>
> 注意：在子类控制器中，如果以`private`或`protected`来修饰方法，可以隐藏该方法，使其无法在 URL 请求中被直接调用

