# 全局事件
EasySwoole有五个全局事件，全部位于框架安装后生成的EasySwooleEvent.php中。  
可参考demo中的[EasySwooleEvent.php](https://github.com/easy-swoole/demo/blob/3.x/EasySwooleEvent.php) 文件

- initialize 框架初始化事件
- mainServerCreate 主服务创建事件
- onRequest Http请求事件
- afterRequest Http响应后事件
-  tcp服务器接收数据响应事件