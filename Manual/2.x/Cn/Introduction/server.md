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

# 热加载

## mac OS

MacOS 下使用 `fswatch` 命令监听文件变更，然后重启服务器，需要先安装命令行工具 `brew install fswatch`

在程序根目录下创建文件`start.sh` 并 `chmod +x start.sh`

然后复制如下shell脚本保存到 `start.sh` 文件夹

```bash
#!/bin/bash
DIR=$1

if [ ! -n "$DIR" ] ;then
    echo "you have not choice Application directory !"
    exit
fi

php easyswoole stop
php easyswoole start --d

fswatch $DIR | while read file
do
   echo "${file} was modify" >> ./Temp/reload.log 2>&1
   php easyswoole reload
done
```
使用方法： `./start.sh ./Application` 

如果直接执行 `./start.sh` 会提示 `you have not choice Application directory`，因为我们需要指定监听路径，通常是`Application` 目录

所以执行命令 `./start.sh ./Application` 监听的路径为相对路径或绝对路径，相对路径注意使用 `./` 开头，否则会监听`Mac OS`里 `/Application` 目录。

启动后脚本会自动启动 `easyswoole` 并进入守护模式，但注意进程还是会hang住，因为 `fswatch` 会不断监听文件变更，如果 `Ctrl+c` 关闭进程则仅关闭了文件监听，`easyswoole` 会依然再后台运行。此时可以手动停止服务或者再次运行热加载脚本。
 
如果需要将热加载脚本也放入后台则使用命令 <code> nohup ./start.sh ./Application &</code> 即可(注意最后有个and符号)。  