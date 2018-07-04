# CURL SSL错误
在低版本的CURL中，若在服务启动前执行CURL一个ssl连接  那么此后在回调函数内再次执行该curl，会报错：
 A PKCS #11 module returned CKR_DEVICE_ERROR, indicating that a problem has occurred with the token or slot.
 若不在服务启动前执行CURL SSL连接，则不报错。
## 相关代码
```
$a = function (){
    $ch = curl_init("https://www.baidu.com");
    $curlOPt = array(
        CURLOPT_CONNECTTIMEOUT=>3,
        CURLOPT_TIMEOUT=>10,
        CURLOPT_AUTOREFERER=>true,
        CURLOPT_USERAGENT=>"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E)",
        CURLOPT_FOLLOWLOCATION=>true,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_SSL_VERIFYPEER=>false,
        CURLOPT_SSL_VERIFYHOST=>false,
        CURLOPT_HEADER=>true,
    );
    curl_setopt_array($ch,$curlOPt);
    $result = curl_exec($ch);
    var_dump(curl_error($ch));
    curl_close($ch);
};

$a();

if(pcntl_fork()){
    $a();
}else{
    $a();
}

```
> swoole中同理。

## 解决方案
更新libcurl至最新的7.5.x,并重新编译php curl拓展。
查看拓展版本：
```
php --ri curl
```

