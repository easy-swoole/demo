## 收到请求事件

```php
protected function onRequest($actionName): ?bool
```

当EasySwoole收到任何的HTTP请求时，均会执行该事件。该事件可以对HTTP请求全局拦截。

```php
$sec = new Security();
if($sec->check($request->getRequestParam())){
   $response->write("do not attack");
   return false;
}
if($sec->check($request->getCookieParams())){
   $response->write("do not attack");
   return false;
}
```

或者是

```php
$cookie = $request->getCookieParams('who');
//do cookie auth
if(auth fail){
   $this->response()->autoEnd(true);
   return false;
}
```

> 若在改事件中，执行 $response->end(),则该次请求不会进入路由匹配阶段。

