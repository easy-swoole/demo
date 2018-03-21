###在Easyswoole中使用protobuf进行数据交互

 **1.安装protoc，用来生成protobuf的php类库文件**

    下载地址https://github.com/google/protobuf/releases
    下载对应的版本进行安装。

 **2.安装php的protobuf扩展**
两种安装方式

    2.1通过pecl安装 pecl install protobuf
    2.2通过编译安装，下载地址

 https://github.com/google/protobuf/tree/master/php
 **3.创建一个Protobuf的文件夹**
 **4. 在Protobuf内新建一个proto的文件 test.proto**
```
    syntax = "proto3";
    package Test;
    message helloworld
    {
        int32 id = 1; // ID
        string str = 2; // str
    }
```
 **5. 通过第一步安装protoc,生成php代码**
```
    protoc --php_out=./Src test.proto
```

 **6. 更新composer的命名空间**


        "autoload": {
        "psr-4": {
            "App\\": "Application/",
            "Test\\" : "Protobuf/Src/Test"
        }
    }


 **7. 测试设置protobuf数据***

```
    $helloWorld = new helloworld();
    $helloWorld->setId(1);
    $helloWorld->setStr('hello world');
    var_dump($helloWorld->getId());
    var_dump($helloWorld->getStr());
```

 **8. 读取protobuf数据**
 
 引入类库
 ```
    composer require google/protobuf
 ```
```
    
    
use Test\helloworld;

class Web extends WebSocketController
{


    function test(?string $actionName)
    {

        $string = $this->client()->getData();
        $helloWorld = new helloworld();
        $helloWorld->mergeFromString($string);
        var_dump($helloWorld->getStr());//打印str
        var_dump($helloWorld->getId());//打印Id
    }
```
 
