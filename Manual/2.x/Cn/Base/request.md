
## 生命周期
Request对象在系统中以单例模式存在，自收到客户端HTTP请求时自动创建，直至请求结束自动销毁。Request对象完全符合[PSR7](psr-7.md)中的所有规范。
## 方法列表
### getRequestParam()
用于获取用户通过POST或者GET提交的参数（注意：若POST与GET存在同键名参数，则以POST为准）。
示例：
```
$data = $request->getRequestParam();
var_dump($data);

$orderId = $request->getRequestParam('orderId');
var_dump($orderId);

$keys = array(
"orderId","type"
);
$mixData = $request->getRequestParam($keys);
var_dump($mixData);
```
### getSwooleRequest()
该方法用于获取当前的swoole_http_request对象。

## PSR-7规范ServerRequest对象中常用方法
### getCookieParams()
该方法用于获取HTTP请求中的cookie信息
```
$all = $request->getCookieParams();
var_dump($all);
$who = $request->getCookieParams('who');
var_dump($who);
```
### getUploadedFiles()
该方法用于获取客户端上传的全部文件。
```
$data = $request->getUploadFiles();
var_dump($data);
```
### getBody()
该方法用于获取以非form-data或x-www-form-urlenceded编码格式POST提交的原始数据，相当于PHP中的$HTTP_RAW_POST_DATA。

### 获得get内容
```
$get = $request->getQueryParams();
```
### 获得post内容
```
$post = $request->getParsedBody();
```
### 获得raw内容
```
$content = $request->getBody()->__toString();
$raw_array = json_decode($content, true);
```

### 获得头部
```
$header = $request->getHeaders();
```
### 获得server
```
$server = $request->getServerParams();

```
### 获得cookie
```
$cookie = $request->getCookieParams();
