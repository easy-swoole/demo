# 常见问题
## 如何获取$HTTP_RAW_POST_DATA
```
$content = $this->request()->getBody()->__toString();
$raw_array = json_decode($content, true);
```
## 如何获取客户端IP
举例，如何在控制器中获取客户端IP
```
//真实地址
$ip = ServerManager::getInstance()->getServer()->connection_info($this->request()->getSwooleRequest()->fd);
var_dump($ip);
//header 地址，例如经过nginx proxy后
$ip2 = $this->request()->getHeaders();
var_dump($ip2);
```
## 如何处理静态资源
Apache URl rewrite
```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  #RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]  fcgi下无效
  RewriteRule ^(.*)$  http://127.0.0.1:9501/$1 [QSA,P,L]
   #请开启 proxy_mod proxy_http_mod request_mod
</IfModule>
```

Nginx URl rewrite
```
server {
    root /data/wwwroot/;
    server_name local.swoole.com;
    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        if (!-e $request_filename) {
             proxy_pass http://127.0.0.1:9501;
        }
    }
}
```
# HTTP 状态码总为500

由于 Swoole 底层的问题，在设置HTTP状态码时发生设置失败，返回状态码为500的情况，例如在控制器中调用 `$this->response()->redirect()` 等方法或手动设置HTTP状态码不生效，请在全局事件内添加以下代码作为临时解决方案

```php
public function onRequest(Request $request, Response $response): void
{
	// 开启后请求结束时框架会对 swoole_http_response 执行一次END操作
	$response->autoEnd(true);
}
```

