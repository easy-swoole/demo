# CURL
为了让开发者能更便捷的调用cURL，easySwoole对cURL进行了封装，先来个例子感受一下

```php
use Core\Utility\Curl\Request;

// 获取快递100接口数据
$param = ['type' => 'zhongtong', 'postid' => '457500981717'];
$url = 'http://www.kuaidi100.com/query?' . http_build_query($param);

// 创建Request对象
$request = new Request($url);

// 获取Response对象
$response = $Request->exec();

// 接口返回内容
$resources = $Response->getBody();
```

没错，就这么简单，这样就完成了一次cURL请求，下面我们来详细的了解一下这里用到的两个对象`Request`和`Response`

## Request对象

### 实例化对象

```
$request = new Request($url,$opt)
```
|参数|类型|是否必须|说明|示例|
|:---:|:---:|:---:|:---|:---|
|url|String|否|需要发起请求的url|http://baidu.com|
|opt|Array|否|cURL的设置数组|[CURLOPT_RETURNTRANSFER => 1]|

### 方法列表

|方法名称|返回类型|说明|
|:---:|:---:|:---:|
| setPost | Request |设置POST参数|
| setUrl | Request |设置要请求的URL|
| setOpt | Request |设置CURL请求参数|
| getOpt | Array |获取当前的CURL设置|
| exec | Response |发起一次请求|

其中 `setPost` `setUrl` `setOpt` 方法支持链式调用

```
$response = $request->setUrl('http://xxx.cn')
　　　　　　　　　　　　->setPost(["Param"=>"Value"])
　　　　　　　　　　　　->setOpt([CURLOPT_COOKIE=>"a=asas;b=asas"])
　　　　　　　　　　　　->exec()
```

#### setPost
如果不调用本方法，默认是发起一个GET请求，调用本方法设置了POST参数后则发起POST请求

```
$request->setPost(
    array(
        "Param"=>"Value",
        "Param"=>"Value"
    )
);
```

#### setUrl
设置要请求的URL，`注意` 这将覆盖实例化时指定的Url

```
$request->setUrl("new url");
```

#### setOpt
设置CURL请求参数，可以用本方法定制更多自定义的CURL参数，如 `COOKIE` `PROXY` 等，如果之前设置过相关的参数，比如已经使用`setPost`方法设置了POST参数，调用本方法时传入了`CURLOPT_POSTFIELDS`则会覆盖之前的设置

```
$request->setOpt(
    array(
        CURLOPT_COOKIE=>"a=asas;b=asas",
    )
);
```

#### getOpt
获取当前已经设置的CURL参数，返回一个数组

```
var_dump($request->getOpt());
```
#### exec
执行一次请求，无论请求执行是否成功都会返回一个`Response`对象

```
$response = $request->exec();
```

## Response对象

请求执行完成后将得到一个`Response`对象，可以从中获取到本次请求的结果

### 方法列表

|方法名称|返回类型|说明|
|:---:|:---:|:---:|
| getBody | Bool Or String |获取请求到的数据|
| getError | String |返回错误信息文本|
| getErrorNo | Int |返回错误信息代码|
| getCurlInfo | Array |获取本次请求的相关信息|
| getHeaderLine | Bool Or String |获取本次请求的原始头部信息|
| getCookies | Array | 获取本次请求的全部Cookie信息|
| getCookie | String | 获取本次请求的单个Cookie信息|

本对象实现了`__toString`魔术方法，将本对象当做字符串使用时获取到的即是包含请求头信息的原始报文

#### getBody
如果请求成功，调用本方法将返回响应内容，不含请求头信息，如果请求失败，则返回`false`

```
// 调用示例
$response->getBody();

// 返回示例
// {"msg": "请求成功", "status": true}
```

#### getError
如果请求成功没有错误，本方法返回空字符串，否则返回详细的错误信息文本

```
// 调用示例
$response->getError();

// 返回示例
// CURLE_SEND_ERROR (55)
```

#### getErrorNo
如果请求成功没有错误，本方法返回`Int`0，否则返回详细的错误代码

```
// 调用示例
$response->getErrorNo();

// 具体错误代码参见: http://php.net/manual/zh/function.curl-errno.php
```

#### getCurlInfo
返回一个数组，包含了本次请求的具体信息，包括`HTTP响应码`,`请求耗时`等

```
// 调用示例
$response->getCurlInfo();

// 具体返回含义参见: http://php.net/manual/zh/function.curl-getinfo.php
```

#### getHeaderLine
返回一个字符串，包含了原始的响应头信息

```
// 调用示例
$response->getHeaderLine();

// 返回示例

// HTTP/1.1 200 OK
// Server: nginx
// Date: Sun, 22 Oct 2017 06:32:16 GMT
// Content-Type: text/html;charset=UTF-8
```

#### getCookies
返回一个数组，包含了本次请求全部的Cookie信息

```
// 调用示例
$response->getCookies();

// 返回示例
array(
	'H_PS_645EC'=>'9f63mNQg2pgtwXyUfTqxRvv9UXs',
	'H_PS_PSSID'=>'1441_13551_21117_24022'
)
```

#### getCookie
传入Cookie的键名，返回Cookie值，如果指定的键名不存在则返回 `null`

```
// 调用示例
$response->getCookies('H_PS_645EC');

// 返回示例
// 9f63mNQg2pgtwXyUfTqxRvv9UXs
```

## UA生成器

考虑到在采集过程中需要伪装UA的情况，比如说采集微信的文章就需要伪装微信的UA，封装了一个`UAGenerate`类用于生成随机的UA

#### 定义常量
------

|常量名称|含义|
|:---:|:---:|
|`SYS_WIN`|表示Windows操作系统|
|`SYS_OSX`|表示macOS操作系统|
|`SYS_IOS`|表示iPhoneOS操作系统(mobile)|
|`SYS_LINUX`|表示Linux操作系统|
|`SYS_ANDROID`|表示AndroidOS操作系统(mobile)|
|`SYS_BIT_X86`|表示32位操作系统(仅针对PC模拟)|
|`SYS_BIT_X64`|表示64位操作系统(仅针对PC模拟)|

#### 基本用法
------

```
UAGenerate::mock(UAGenerate::SYS_OSX, false, UAGenerate::SYS_BIT_X64);
```

- 参数1 : 需要生成的操作系统，可以为空，默认随机从以上5种操作系统(包含手机)中随机选择
- 参数2 : 是否添加微信UA，可以为空，默认不添加
- 参数3 : 需要生成的操作系统版本，可以为空，默认32位/64位随机选择，只生效PC操作系统

#### 生成样例
------

```
// 安卓
Mozilla/5.0 (Linux; Android 4.4.4; Mobile) Gecko/20100101 Firefox/42.0
Mozilla/5.0 (Linux; Android 4.1.2; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.1444.0 Safari/537.36

// 安卓微信
Mozilla/5.0 (Linux; Android 4.1.2; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.1822.0 Safari/537.36 Mobile MicroMessenger/5.9.122

// iOS
Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) Gecko/20100101 Firefox/55.0
Mozilla/5.0 (iPhone; CPU iPhone OS 9_0 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.1957.0 Safari/537.36

// iOS微信
Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) Gecko/20100101 Firefox/55.0 Mobile MicroMessenger/5.9.122

// Windows
Mozilla/5.0 (Windows NT 6.3; WOW64 ) Gecko/20100101 Firefox/49.0
Mozilla/5.0 (Windows NT 5.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.1907.0 Safari/537.36

// macOS
Mozilla/5.0 (Macintosh; Intel x86_64 Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.1232.0 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) Gecko/20100101 Firefox/53.0

// Linux
Mozilla/5.0 (X11; Ubuntu; x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.1189.0 Safari/537.36
Mozilla/5.0 (X11; Centos; x86_64) Gecko/20100101 Firefox/51.0

```


<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>
