# 系统内核优化
## ulimit

设置系统打开文件数设置，解决高并发下 too many open files 问题。此选项直接影响单个进程容纳的客户端连接数。
Soft open files 是Linux系统参数，影响系统单个进程能够打开最大的文件句柄数量，这个值会影响到长连接应用如聊天中单个进程能够维持的用户连接数， 运行ulimit -n能看到这个参数值，如果是1024，就是代表单个进程只能同时最多只能维持1024甚至更少（因为有其它文件的句柄被打开）。如果开启4个进程维持用户连接，那么整个应用能够同时维持的连接数不会超过4*1024个，也就是说最多只能支持4x1024个用户在线可以增大这个设置以便服务能够维持更多的TCP连接。
Soft open files 修改方法：

### ulimit -HSn 102400
这只是在当前终端有效，退出之后，open files 又变为默认值。

### 将ulimit -HSn 102400写到/etc/profile中
这样每次登录终端时，都会自动执行/etc/profile。

### 令修改open files的数值永久生效。
修改配置文件：/etc/security/limits.conf. 在这个文件后加上：
```
soft nofile 1024000
hard nofile 1024000
root soft nofile 1024000
root hard nofile 1024000
```
> 注意，修改limits.conf文件后，需要重启系统生效

## sysctl.conf

打开文件 /etc/sysctl.conf，增加以下设置

```
#该参数设置系统的TIME_WAIT的数量，如果超过默认值则会被立即清除
net.ipv4.tcp_max_tw_buckets = 20000
#定义了系统中每一个端口最大的监听队列的长度，这是个全局的参数
net.core.somaxconn = 65535
#对于还未获得对方确认的连接请求，可保存在队列中的最大数目
net.ipv4.tcp_max_syn_backlog = 262144
#在每个网络接口接收数据包的速率比内核处理这些包的速率快时，允许送到队列的数据包的最大数目
net.core.netdev_max_backlog = 30000
#能够更快地回收TIME-WAIT套接字。此选项会导致处于NAT网络的客户端超时，建议为0
net.ipv4.tcp_tw_recycle = 0
#系统所有进程一共可以打开的文件数量
fs.file-max = 6815744
#防火墙跟踪表的大小。注意：如果防火墙没开则会提示error: "net.netfilter.nf_conntrack_max" is an unknown key，忽略即可
net.netfilter.nf_conntrack_max = 2621440
```

> 运行 sysctl -p即可生效。

#### 说明：

/etc/sysctl.conf 可设置的选项很多，其它选项可以根据自己的环境需要进行设置
net.unix.max_dgram_qlen = 100

swoole使用unix socket dgram来做进程间通信，如果请求量很大，需要调整此参数。系统默认为10，可以设置为100或者更大。
或者增加worker进程的数量，减少单个worker进程分配的请求量。
net.core.wmem_max

修改此参数增加socket缓存区的内存大小

```
net.ipv4.tcp_mem  =   379008       505344  758016
net.ipv4.tcp_wmem = 4096        16384   4194304
net.ipv4.tcp_rmem = 4096          87380   4194304
net.core.wmem_default = 8388608
net.core.rmem_default = 8388608
net.core.rmem_max = 16777216
net.core.wmem_max = 16777216
net.ipv4.tcp_tw_reuse
```

是否socket reuse，此函数的作用是Server重启时可以快速重新使用监听的端口。如果没有设置此参数，会导致server重启时发生端口未及时释放而启动失败
net.ipv4.tcp_tw_recycle

使用socket快速回收，短连接Server需要开启此参数。此参数表示开启TCP连接中TIME-WAIT sockets的快速回收，Linux系统中默认为0，表示关闭。打开此参数可能会造成NAT用户连接不稳定，请谨慎测试后再开启。
其他重要配置

    net.ipv4.tcp_syncookies=1
    net.ipv4.tcp_max_syn_backlog=81920
    net.ipv4.tcp_synack_retries=3
    net.ipv4.tcp_syn_retries=3
    net.ipv4.tcp_fin_timeout = 30
    net.ipv4.tcp_keepalive_time = 300
    net.ipv4.tcp_tw_reuse = 1
    net.ipv4.tcp_tw_recycle = 1
    net.ipv4.ip_local_port_range = 20000 65000
    net.ipv4.tcp_max_tw_buckets = 200000
    net.ipv4.route.max_size = 5242880

## 开启CoreDump

设置内核参数

```
kernel.core_pattern = /data/core_files/core-%e-%p-%t
```

通过ulimit -c命令查看当前coredump文件的限制

```
ulimit -c
```

如果为0，需要修改/etc/security/limits.conf，进行limit设置。

开启core-dump后，一旦程序发生异常，会将进程导出到文件。对于调查程序问题有很大的帮助
查看配置是否生效

如：修改net.unix.max_dgram_qlen = 100后，通过

```
cat /proc/sys/net/unix/max_dgram_qlen
```

如果修改成功，这里是新设置的值。