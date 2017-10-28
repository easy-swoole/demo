爬虫UA生成器
------

生成一个随机的UserAgent供爬虫使用，支持伪装微信UA

#### 定义常量
------

|常量名称|含义|
|:---:|:---:|
|`SYS_WIN`|表示Windows操作系统|
|`SYS_OSX`|表示macOS操作系统|
|`SYS_IOS`|表示iPhoneOS操作系统(mobile)|
|`SYS_LINUX`|表示Linux操作系统|
|`SYS_ANDROID`|表示AndroidOS操作系统(mobile)|
|`SYS_BIT_X86`|表示32位操作系统(仅针对PC模拟)|
|`SYS_BIT_X64`|表示64位操作系统(仅针对PC模拟)|

#### 基本用法
------

```
UASimulator::mock(UASimulator::SYS_OSX, false, UASimulator::SYS_BIT_X64);
```

- 参数1 : 需要生成的操作系统，可以为空，默认随机从以上5种操作系统(包含手机)中随机选择
- 参数2 : 是否添加微信UA，可以为空，默认不添加
- 参数3 : 需要生成的操作系统版本，可以为空，默认32位/64位随机选择，只生效PC操作系统

#### 生成样例
------

```
// 安卓
Mozilla/5.0 (Linux; Android 4.4.4; Mobile) Gecko/20100101 Firefox/42.0
Mozilla/5.0 (Linux; Android 4.1.2; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.1444.0 Safari/537.36

// 安卓微信
Mozilla/5.0 (Linux; Android 4.1.2; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.1822.0 Safari/537.36 Mobile MicroMessenger/5.9.122

// iOS
Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) Gecko/20100101 Firefox/55.0
Mozilla/5.0 (iPhone; CPU iPhone OS 9_0 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.1957.0 Safari/537.36

// iOS微信
Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) Gecko/20100101 Firefox/55.0 Mobile MicroMessenger/5.9.122

// Windows
Mozilla/5.0 (Windows NT 6.3; WOW64 ) Gecko/20100101 Firefox/49.0
Mozilla/5.0 (Windows NT 5.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.1907.0 Safari/537.36

// macOS
Mozilla/5.0 (Macintosh; Intel x86_64 Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.1232.0 Safari/537.36
Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) Gecko/20100101 Firefox/53.0

// Linux
Mozilla/5.0 (X11; Ubuntu; x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.1189.0 Safari/537.36
Mozilla/5.0 (X11; Centos; x86_64) Gecko/20100101 Firefox/51.0

```