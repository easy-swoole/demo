# 权限验证
任何控制器请求，都会执行控制器的onRequest函数，当你的某个控制器需要对权限进行验证的时候，那么请在子类中重写该函数。
```php
protected function onRequest($action): ?bool
{
    if(auth_fail){
        $this->response()->write('auth fail');
        return false;
    }else{
        return true or null;
    }
}
```
> 该函数一定要有返回值，仅当返回false的时候为拦截请求，不再响应后续的action行为。可以参考
https://github.com/easy-swoole/demo/blob/master/Application/HttpController/Api/User.php
