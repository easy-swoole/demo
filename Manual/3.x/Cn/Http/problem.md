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
自 swoole **1.10.x** 和 **2.1.x** 版本存起，未执行http server回调中，若未执行response->end(),则全部返回500状态码

# 如何setCookie  
调用response对象的setCookie方法即可设置cookie
```php
  $this->response()->setCookie('name','value');
```
更多操作可看[Response对象](response.md)


# 如何自定义App名称
只需要修改composer.json的命名空间注册就行
```
    "autoload": {
        "psr-4": {
            "App\\": "Application/"
        }
    }
```