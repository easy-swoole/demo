# Swoole Http Server
You can create a HTTP Server with Swoole :
```
//test.php
$http = new swoole_http_server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$http->start();
```
> php test.php and curl 127.0.0.1:9501 for testing the server .

## Use Swoole Http Server to handle Api Request

### Nginx 
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
### Apache
```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$  http://127.0.0.1:9501/$1 [QSA,P,L]
  #require proxy_mod proxy_http_mod requset_mod
</IfModule>
```

## FQA
### CURL post big data to Swoole Server timeout
Before post a big data request,curl would sent a 100-continue request to server , unfortunately , Swoole http server do not 
support 100-continue,so you may try some measures to get rid of the 100-continue request .
- In php
```
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:9501");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));// look here
curl_setopt($ch, CURLOPT_POSTFIELDS, array('test' => str_repeat('a', 800000)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
```
- Use Nginx
- Recompile Swoole extension.Clear the annotation for SW_HTTP_100_CONTINUE in swoole_config.h ，But it will take more of the cpu times after you enable 100-CONTINUE.

### Max size for GET/POST request

- GET,the max size for get request is 8k,otherwise it may case:
    ```
       WARN swReactorThread_onReceive_http_request: http header is too long.
    ```
- POST,the max size for POST request is 2M，you can change it by set package_max_length config option 。
    > swoole server max memory usage =  max concurrency num * package_max_length
    
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>