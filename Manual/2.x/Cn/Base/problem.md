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
```