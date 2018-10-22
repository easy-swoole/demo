# 关于跨域处理

在全局事件添加以下代码 拦截所有请求添加跨域头

```php
public static function onRequest(Request $request, Response $response): bool
{
    // TODO: Implement onRequest() method.
    $response->withHeader('Access-Control-Allow-Origin', '*');
    $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    $response->withHeader('Access-Control-Allow-Credentials', 'true');
    $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    if ($request->getMethod() === 'OPTIONS') {
        $response->withStatus(Status::CODE_OK);
        $response->end();
    }
    return true;
}
```