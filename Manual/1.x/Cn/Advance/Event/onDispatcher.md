请求分发事件
------

```
function onDispatcher(Request $request,Response $response,$targetControllerClass,$targetAction);
```

HTTP请求进来后，easySwoole会对请求进行解析以及分发，当找到对应的控制器后将会执行本事件

> 注意: 如果请求无法解析到对应的控制器，或控制器不是继承自`AbstractController`将不会执行本事件
