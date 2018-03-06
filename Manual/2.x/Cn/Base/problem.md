# 常见问题
## 如何获取$HTTP_RAW_POST_DATA
## 如何结束响应
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