## HTTP控制器

控制器决定了一个请求进来，应该如何被处理，控制器即是应用程序的心脏，一个控制器就是一个类文件，当请求进来，定位到控制器方法，就会执行其中的代码

## 控制器的命名和文件路径

控制器文件以及类的命名遵循大驼峰法(CamelCase)，统一存放于 `App\HttpController` 目录下，让我们举一个例子，来感受一下控制器文件、类、与访问的URL之间互相的联系，假设有一个后台添加用户的操作

```
# 访问这个操作方法的路径
http://localhost:9501/admin/user/add

# 控制器类文件的路径
App\HttpController\Admin\User.php

# 对应的方法
App\HttpController\Admin\User::add()
```

可见控制器文件与访问的路径，在默认的情况下，应该是互相对应的，下面的代码例子可能不会对应的列出访问的URL以及类文件的位置，参考上面的例子来放置和访问

## 第一个控制器

让我们从一个简单的例子，开始easySwoole的编码之旅，打开你的编辑器，输入以下代码

```php
<?php

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Hello extends Controller
{
    function index()
    {
        $this->response()->write('Hello easySwoole!');
    }
}
```

然后将其保存到 `App\HttpController` 文件夹，文件名和类名应该是一致的，并且保持一致的大小写，然后使用类似这样的 URL 来访问这个控制器

```
http://localhost:9501/hello
```

如果做得没错，应该看到页面上打印出了 **Hello easySwoole!** 的字样

同时应该确保所有的控制器都**继承自** `EasySwoole\Core\Http\AbstractInterface\Controller` 这个父控制器类，以便获得其中的操作方法

## URL访问

可能你已经注意到了，上面的类中定义了一个index方法，但是我们访问的URL中并没有指明这个方法，如果在 URI 中不能解析出正确的 actionName (即对应的方法名称)，将会定位到index方法来执行，也可以将URL写成这样来访问这个控制器

```
http://localhost:9501/hello/index
```

让我们再来试一下，在控制器中添加一个新的方法 `article` 并在浏览器中访问它

```php
<?php

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Hello extends Controller
{
    function index()
    {
        $this->response()->write('Hello easySwoole!');
    }
  
    function article()
    {
        $this->response()->write('this is article');
    }
}
```

同样的，我们可以使用类似下面的 URL 来访问到这个方法

```
http://localhost:9501/hello/article
```

> 注意 : 因为 Swoole 常驻内存的特性，在修改了代码后，需要重新启动框架才能使新的代码生效，在控制台结束运行，一般可以按下 Ctrl+C 组合键来结束，并且执行 php easyswoole start 来重新启动框架

如果不带任何路径的访问，比如说访问下面的路径

```
http://localhost:9501/
```

将会定位到默认的控制器 `App\HttpController\Index` 并且定位到其默认的 `index` 方法，现在你已经掌握了控制器的基本定义与访问，接下来让我们了解一下控制器父类，具体提供了哪些方法

## 控制器父类的方法

控制器父类提供了几个方法，允许开发者去覆盖，并且在特定情况下执行一些开发者自定义的逻辑

- index()

  控制器中默认存在方法，当在URL中无法解析出actionName时，将默认执行该方法。例如有一个Test控制器，当访问domain/test路径时，则默认解析为index

- actionNotFound($action)

  当在URL中解析出actionName，而在控制器中无存在对应方法（函数）时，则执行该方法。例如有一个Test控制器，当访问domain/test/test1/index.html路径时，actionName会被解析为test1，而此时若控制器中无test1方法时，则执行actionNotFound

- onRequest($action)

  当一个URL请求进来，能够被映射到控制器且做完actionName解析后，将立马执行OnRequest事件，以便对请求做预处理，如权限过滤等。注意，该事件与全局事件中的 onRequest 并不冲突，全局事件中每个请求都会执行，而控制器方法中的 onRequest 只有当前控制器被访问时才会执行，该方法需要返回布尔值`true`才会继续执行被请求的方法，如果返回了`false`则不再执行被请求的方法内容

- afterAction($actionName)

  在任何的控制器响应结束后，均会执行该事件,该事件预留于做分析记录

- onException(\Throwable $throwable,$actionName)

  控制器逻辑中发生异常时，会被本方法拦截到，同时框架向该方法传入Throwable对象以及当前请求的方法名称，以便开发者对当前控制器发生的异常做自定义处理以及日志记录等

另外还提供了一些实用方法如下：

- getActionName()

  获得当前被请求的方法名称

- writeJson($statusCode = 200,$result = null,$msg = null)

  直接输出 Json 数据到浏览器

- validateParams(Rules $rules)

  校验当前请求参数


## 请求和响应

控制器提供了两个方法，获取本次访问的请求和响应对象

- request()

  获取到本次访问的 EasySwoole\Core\Http\Request 对象

- response()

  获取到本次访问的 EasySwoole\Core\Http\Response 对象
