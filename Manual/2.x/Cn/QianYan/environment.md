# 环境要求

满足基本的环境要求才能运行框架，easySwoole 框架对环境的要求十分简单，只需要满足运行 Swoole 拓展的条件，并且 PHP 版本在 7.2 以上即可

## 基础运行环境

- 保证 **PHP** 版本大于等于 **7.2**


- 保证 **Swoole** 拓展版本大于等于 **1.9.11**
- 使用 **Linux** / **FreeBSD** / **MacOS** 这三类操作系统
- 使用 **Composer** 作为依赖管理工具

------

参考下面的建议，它们都不是必须的，但是有助于更高效的使用框架和进行开发

- 使用 **Ubuntu14** / **CentOS 6.5** 或更高版本操作系统

## Swoole 拓展安装教程

如果还没有安装 Swoole 拓展，可以参考下面的安装步骤来进行安装，这里介绍几种常用的安装方法，在生产环境建议使用 **1.9.x** 版本的 Swoole 拓展部署

### 使用 PECL 快速安装

PECL 是一个PHP的拓展仓库，可以很方便的安装各种拓展，注意版本是可以指定的，替换短横线后面的版本即可，执行下面的命令行进行安装

```bash
pecl install swoole-1.9.23
```

扩展自动安装完成后，还需要编辑 **php.ini** 文件，在文件的最后面加入以下内容

```ini
[swoole]
extension=swoole.so
```

### 源码编译安装

可以从下面列出的任一地址下载到 Swoole 拓展的源码

- [https://github.com/swoole/swoole-src/releases](https://github.com/swoole/swoole-src/releases) 
- [http://pecl.php.net/package/swoole](http://pecl.php.net/package/swoole)
- [http://git.oschina.net/swoole/swoole](http://git.oschina.net/swoole/swoole)

将下载到的源代码解压到任意目录，并且进入目录，分别执行以下命令进行编译

```bash
phpize
./configure
make
make && install
```

编译完成后，同样需要找到 **php.ini** 文件，在文件的最后面加入以下内容

```ini
[swoole]
extension=swoole.so
```

安装完成后可以通过命令行执行 `php -m` 确认是否成功加载了 Swoole 拓展，列出的模块列表中含有swoole模块就是加载成功了