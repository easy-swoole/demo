# Env Require
- PHP : 5.6.30 or higher
- Swoole : 1.9.x

> Suggest to use Ubuntu14/CentOS 6.5 or higher OS

# Install Swoole Ext
## Linux users
> Swoole is available as a PECL compatible package

```
#!/bin/bash
pecl install swoole
```

## MacOS X (macOS) users

> It is highly recommended to install Swoole on Mac OS X or macOS systems via homebrew

```
#!/bin/bash
brew install swoole
```

## Building swoole from sources
Download link

> You are recommended to download the latest stable version of swoole. You can download the source code from one of the following the links.

    https://github.com/swoole/swoole-src/releases
    http://pecl.php.net/package/swoole

### Steps of Compilation

> The process of compile and install the swoole extension for PHP

```
cd swoole          #  enter the directory of swoole source code   
phpize             #  prepare the build environment for a PHP extension
./configure        #  add configuration paramaters as needed
make               #  a successful result of make is swoole/module/swoole.so
sudo make install  #  install the swoole into the PHP extensions directory
```

### Enable swoole

> After installing the swoole extension to the PHP extensions directory, you will need to edit php.ini and add an extension=swoole.so line before you can use the swoole extension.

```
php -i | grep php.ini                      # check the php.ini file location
sudo echo "extension=swoole.so" > php.ini  # add the extension=swoole.so to the end of php.ini
php -m | grep swoole                       # check if the swoole extension has been enabled
```

## More About Swoole
See [https://www.swoole.co.uk/](https://www.swoole.co.uk/)


<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>