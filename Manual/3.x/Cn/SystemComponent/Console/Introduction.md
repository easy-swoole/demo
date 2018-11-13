#EasySwoole CONSOLE组件  

EasySwoole 提供了console控制台组件,在项目运行的时候,可通过命令和服务端进行通讯,查看服务端运行状态,实时推送运行逻辑等

示例:  
```
php easyswoole console
server status 
server hostIp
```


## 配置
console配置如下:
```
CONSOLE.ENABLE = true              #是否开启console
CONSOLE.LISTEN_ADDRESS = 127.0.0.1 #console服务端监听地址
CONSOLE.HOST = 127.0.0.1           #console客户端连接远程地址
CONSOLE.PORT = 9000                #console服务端监听端口,客户端连接远程端口
CONSOLE.EXPIRE = 120               #心跳超时时间
CONSOLE.AUTH = auth_password       #鉴权密码,如不需要鉴权可设置null
CONSOLE.PUSH_LOG = true            #是否推送日志
```

