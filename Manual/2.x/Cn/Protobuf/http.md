# HTTP中使用protobuf

### 1. 安装protoc命令
    下载地址https://github.com/google/protobuf/releases
    根据自己系统对应下载

### 2.安装php protocbuf扩展
通过pecl方式安装
VERSION 目前最新版本为3.5.1.1
sudo pecl install protobuf-3.5.1.1
### 3.创建test.proto文件
在项目根目录创建Protobuf/Src目录下 创建test.proto 文件
代码如下:
```
syntax = "proto3";
package kjcx;

message helloworld
{
    int32 id = 1; // ID
    string str = 2; // str
}
```

### 4.进入到src目录下执行如下命令生成php文件
protoc --php_out=./ test.proto

### 5.修改composer.json文件
将命名空间Kjcx 定义到Protobuf/Src/Kjcx/目录
GPBMetadata定义到Protobuf/Src/GPBMetadata目录下
```
"autoload": {
        "psr-4": {
            "App\\": "Application/",
            "Kjcx\\": "Protobuf/Src/Kjcx/",
             "GPBMetadata\\" :"Protobuf/Src/GPBMetadata"
        }
    }
```

```
之后执行composer update
```
### 6.解析protobuf 还需引入类库
通过composer引入到项目中

```
composer require google/protobuf
```


### 7.测试代码
在httpController下Index控制器执行如下代码

```
use Kjcx\helloworld;

class Index extends Controller
{

    //测试路径 /index.html
    function index()
    {

        $kjcx = new helloworld();
        $kjcx->setId(1);
        $kjcx->setStr('test');
        $this->response()->write($kjcx->getStr() . $kjcx->getId());
    }
    
    //部分代码...
```

### 8.测试访问

浏览器输入:http://localhost:9501
输出test1