# CURL
easySwoole对CURL进行了封装，以方便开发者更加方便的进行调用。
## Request对象。
```
$request = new Request("target url");
```
### 方法列表
#### setPost
```
$request->setPost(
    array(
        "col1"=>"col1",
        "col2"=>"col2"
    )
);
```
#### setOpt
```
$request->setOpt(
    array(
        CURLOPT_COOKIE=>"a=asas;b=asas",
    )
);
```
#### setUrl
```
$request->setUrl("new url");
```
#### getOpt
```
var_dump($request->getOpt());
```
#### exec
```
$response = $request->exec();
```
## Response
### 方法列表
#### getBody
#### getError
#### getErrorNo
#### getCurlInfo
#### getHeaderLine
#### getCookies
#### getCookie
#### __toString

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>