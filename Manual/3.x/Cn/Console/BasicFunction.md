## 基础功能
console组件提供了3个基础控制器

### Auth  
Auth提供了鉴权功能,通过配置auth秘钥开启鉴权,  
当开启鉴权后,使用php easyswoole console连接成功后,必须发送:
```
auth 鉴权秘钥
```
鉴权成功之后才可继续操作


### Help
Help提供了基础的帮助文档,以及可获取以注册的命令帮助
```
help
```

输出:
```
欢迎使用EASYSWOOLE远程控制台!
用法: 命令 [命令参数]

请使用 help [命令名称] 获取某个命令的使用帮助，当前已注册的命令:

help
auth
server

```
获取注册的命令帮助:
```
help server
```
输出:
```
help server
进行服务端的管理

用法: 命令 [命令参数]

status                    | 查看服务当前的状态
hostIp                    | 显示服务当前的IP地址
reload                    | 重载服务端
shutdown                  | 关闭服务端
close                     | 断开远程连接
clientInfo [fd]           | 查看某个链接的信息
serverList                | 查看服务端启动的服务列表
pushLog [enable|disable]  | 打开或关闭远程日志推送
```


### Server  
Server控制器提供了一系列的服务端监控管理命令:
```
server status           | 查看服务当前的状态
server hostIp           | 显示服务当前的IP地址
server reload           | 重载服务端
server shutdown         | 关闭服务端
server close            | 断开远程连接
server clientInfo       | 查看某个链接的信息
server serverList       | 查看服务端启动的服务列表
server pushLog          | 打开或关闭远程日志推送
```


