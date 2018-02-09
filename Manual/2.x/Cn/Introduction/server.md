# 服务管理脚本
执行完框架安装后，可以在你的项目根目录下，看多一个easyswoole的文件。
执行以下命令：
```
php easyswoole
```
可见：
```
 ______                          _____                              _
 |  ____|                        / ____|                            | |
 | |__      __ _   ___   _   _  | (___   __      __   ___     ___   | |   ___
 |  __|    / _` | / __| | | | |  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \
 | |____  | (_| | \__ \ | |_| |  ____) |  \ V  V /  | (_) | | (_) | | | |  __/
 |______|  \__,_| |___/  \__, | |_____/    \_/\_/    \___/   \___/  |_|  \___|
                          __/ |
                         |___/

欢迎使用为API而生的 easySwoole 框架 当前版本: 2.x

使用:
  easyswoole [操作] [选项]

操作:
  install       安装easySwoole
  start         启动easySwoole
  stop          停止easySwoole
  reload        重启easySwoole
  help          查看命令的帮助信息

有关某个操作的详细信息 请使用 help 命令查看 
如查看 start 操作的详细信息 请输入 easyswoole help --start
```

## 服务启动
开发模式： 
```
php easyswoole start
```
生产环境（守护模式）
```
php easyswoole start --d
```
> 注意是两个-

## 服务停止
```
php easyswoole stop
```
> 注意，守护模式下才需要stop，不然control+c或者是终端断开就退出进程了

## 服务重启
## 服务停止
```
php easyswoole reload
```
> 注意，守护模式下才需要reload，不然control+c或者是终端断开就退出进程了

