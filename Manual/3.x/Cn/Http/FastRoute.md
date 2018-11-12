##自定义路由
EasySwoole支持自定义路由,其路由利用[fastRoute](https://github.com/nikic/FastRoute)实现，因此其路由规则与其保持一致，该组件的详细文档请参考 [GitHub文档](https://github.com/nikic/FastRoute/blob/master/README.md)  
### 示例代码:  
新建文件App\HttpController\Router.php:  
demo地址[Router.php](https://github.com/easy-swoole/demo/blob/3.x/App/HttpController/Router.php)
```php
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/15
 * Time: 上午10:39
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // TODO: Implement initialize() method.
        $routeCollector->get('/user','/index.html');
        $routeCollector->get('/test','/Index/test');
        $routeCollector->get('/rpc','/Rpc/index');
    
        $routeCollector->get('/',function (Request $request ,Response $response){
            $response->write('this router index');
        });
        // /test/index.html
        $routeCollector->get('/test',function (Request $request ,Response $response){
            $response->write('this router test');
            $response->end();
        });
        // /user/1/index.html
        $routeCollector->get( '/user/{id:\d+}',function (Request $request ,Response $response,$id){
            $response->write("this is router user ,your id is {$id}");
            $response->end();
        });

    }
}
```
访问127.0.0.1:9501/rpc,对应为App\HttpController\Rpc.php->index()  
> 注意：若在路由回调函数中不结束该请求响应，则该次请求将会继续进行Dispatch并尝试寻找对应的控制器进行响应处理。

### 全局模式拦截
在Router.php加入以下代码,即可开启全局模式拦截
```php
$this->setGlobalMode(true);
```
全局模式拦截下,路由将只匹配Router.php中的控制器方法响应,将不会执行框架的默认解析

### 异常错误处理  
通过以下2个方法,可设置路由匹配错误以及未找到方法的回调:
```php
$this->setMethodNotAllowCallBack(function (Request $request,Response $response){
    $response->write('未找到处理方法');
});
$this->setRouterNotFoundCallBack(function (Request $request,Response $response){
    $response->write('未找到路由匹配');
});
```
>该回调函数只针对于fastRoute未匹配状况,如果回调里面不结束该请求响应,则该次请求将会继续进行Dispatch并尝试寻找对应的控制器进行响应处理。  



### fastRoute使用 

addRoute方法
------

定义路由的`addRoute`方法原型如下，该方法需要三个参数，下面围绕这三个参数我们对路由组件进行更深一步的了解

```
$routeCollector->addRoute($httpMethod, $routePattern, $handler)
```

#### httpMethod
------
该参数需要传入一个大写的HTTP方法字符串，指定路由可以拦截的方法，单个方法直接传入字符串，需要拦截多个方法可以传入一个一维数组，如下面的例子：

```
// 拦截GET方法
$routeCollector->addRoute('GET', '/router', '/Index');

// 拦截POST方法
$routeCollector->addRoute('POST', '/router', '/Index');

// 拦截多个方法
$routeCollector->addRoute(['GET', 'POST'], '/router', '/Index');

```

#### routePattern
------
传入一个路由匹配表达式，符合该表达式要求的路由才会被拦截并进行处理，表达式支持{参数名称:匹配规则}这样的占位符匹配，用于限定路由参数

#### 基本匹配

下面的定义将会匹配 `http://localhost:9501/users/info`

```
$routeCollector->addRoute('GET', '/users/info', 'handler');
```

#### 绑定参数
下面的定义将`/users/`后面的部分作为参数，并且限定参数只能是数字`[0-9]`

```
// 可以匹配: http://localhost:9501/users/12667
// 不能匹配: http://localhost:9501/users/abcde

$routeCollector->addRoute('GET', '/users/{id:\d+}', 'handler');

```

下面的定义不做任何限定，仅将匹配到的URL部分获取为参数

```
// 可以匹配: http://localhost:9501/users/12667
// 可以匹配: http://localhost:9501/users/abcde

$routeCollector->addRoute('GET', '/users/{name}', 'handler');
```

有时候路由的部分位置是可选的，可以像下面这样定义

```
// 可以匹配: http://localhost:9501/users/to
// 可以匹配: http://localhost:9501/users/to/username

$routeCollector->addRoute('GET', '/users/to[/{name}]', 'handler');
```

获取路由中绑定的参数有两种情况

1. `handler`传入了一个闭包，Dispatch会将绑定的参数按顺序传给闭包
2. `handler`传入了一个控制器路径，Dispatch会将绑定的参数附加给Request对象

利用该方法还可以实现请求`/router/fun1/{id:\d+}`但是符合一定条件的请求需要分发给`/router/fun2/{id:\d+}`处理的情况

```
// 在闭包中使用
$routeCollector->addRoute('GET', '/router/{id:\d+}', function ($id) {
	$Response = Response::getInstance();
	$Response->write('Userid : ' . $id);
	$Response->end();
});

// --------------------------------------------------------

// 直接调用控制器方法
$routeCollector->addRoute('POST', '/router/{id:\d+}', '/Index');
// 此时可以在控制器中调用Request对象存放的参数
$id = $this->request()->getQueryParam('id');

// --------------------------------------------------------

// 绑定参数并跳转到控制器
$routeCollector->addRoute('GET', '/router2/{id:\d+}', function ($id) {
	// 将请求参数附加到Request
	Request::getInstance()->withQueryParams(['id' => $id]);
	// 按自己的处理逻辑转发请求给控制器
	Response::getInstance()->forward('/Index');
});

```

#### handler
------
指定路由匹配成功后需要处理的方法，可以传入一个闭包，当传入闭包时一定要**注意处理完成之后要处理结束响应**否则请求会继续Dispatch寻找对应的控制器来处理，当然如果利用这一点，也可以对某些请求进行处理后再交给控制器执行逻辑

```
// 传入闭包的情况
$routeCollector->addRoute('GET', '/router/{id:\d+}', function ($id) {
	$Response = Response::getInstance();
	$Response->write('Userid : ' . $id);
	$Response->end();
});

```

也可以直接传入控制器路径

```
$routeCollector->addRoute('GET', '/router2/{id:\d+}', '/Index');
```

> 更多使用详情请直接查看FastRouter。
