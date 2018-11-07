# 配置文件

EasySwoole框架提供了非常灵活自由的全局配置功能，配置文件采用PHP返回数组方式定义，对于一些简单的应用，无需修改任何配置，对于复杂的要求，还可以自行扩展自己独立的配置文件和进行动态配置

## 默认配置文件

框架安装完成后系统默认的全局配置文件是项目根目录下的 `dev.env`,`produce.env` 文件，
文件内容如下:

```env
# eg:
# mysql.port = 3306
# MAIN_SERVER.PORT = 80
# MAIN_SERVER.SETTING.worker_num = 80

################ defalut config ##################
SERVER_NAME = EasySwoole

MAIN_SERVER.HOST = 0.0.0.0
MAIN_SERVER.PORT = 9501
MAIN_SERVER.SERVER_TYPE = WEB_SERVER ## 可选为 SERVER  WEB_SERVER WEB_SOCKET_SERVER
MAIN_SERVER.SOCK_TYPE = SWOOLE_TCP  ## 该配置项当为SERVER_TYPE值为SERVER时有效
MAIN_SERVER.RUN_MODEL = SWOOLE_PROCESS

MAIN_SERVER.SETTING.task_worker_num = 8
MAIN_SERVER.SETTING.task_max_request = 500
MAIN_SERVER.SETTING.worker_num = 8
MAIN_SERVER.SETTING.max_request = 5000

TEMP_DIR = null
LOG_DIR = null
```

各项目的配置含义如下

- **MAIN_SERVER**  -  默认Server配置
  - **HOST**  -  默认Server监听的地址
  - **PORT**  -  默认Server监听的端口
  - **SERVER_TYPE**  -  默认Server的类型
  - **SOCK_TYPE**  -  默认Server的Sock类型（ 仅 SERVER_TYPE 配置为 SERVER 时有效 ）
  - **RUN_MODEL**  -  默认Server的运行模式
  - **SETTING**  -  Swoole Server的运行配置（ 完整配置可见[Swoole文档](https://wiki.swoole.com/wiki/page/274.html) ）
    - **task_worker_num**  -  运行的 task_worker 进程数量
    - **task_max_request**  -  task_worker 完成该数量的请求后将退出，防止内存溢出
    - **worker_num**  -  运行的 worker 进程数量
    - **max_request**  -  worker 完成该数量的请求后将退出，防止内存溢出
- **TEMP_DIR**  -  临时文件存放的目录
- **LOG_DIR**  -  日志文件存放的目录

## 配置操作类

配置操作类为 `EasySwoole\Config` 类，使用非常简单，见下面的代码例子，操作类还提供了 `toArray` 方法获取全部配置，`load` 方法重载全部配置，基于这两个方法，可以自己定制更多的高级操作

> 设置和获取配置项都支持点语法分隔，见下面获取配置的代码例子

```php
<?php

$instance = \EasySwoole\EasySwoole\Config::getInstance();

// 获取配置 按层级用点号分隔
$instance->getConf('MAIN_SERVER.SETTING.task_worker_num');

// 设置配置 按层级用点号分隔
$instance->setConf('DATABASE.host', 'localhost');

// 获取全部配置
$conf = $instance->getConf();

// 用一个数组覆盖当前配置项
$conf['DATABASE'] = [
    'host' => '127.0.0.1',
    'port' => 13306
];
$instance->load($conf);
```
> 需要注意的是 由于进程隔离的原因 在Server启动后，动态新增修改的配置项，只对执行操作的进程生效，如果需要全局共享配置需要自己进行扩展

## 添加用户配置项

每个应用都有自己的配置项，添加自己的配置项非常简单，其中一种方法是直接在配置文件中添加即可，如下面的例子

```env
# eg:
# mysql.port = 3306
# MAIN_SERVER.PORT = 80
# MAIN_SERVER.SETTING.worker_num = 80

################ defalut config ##################
SERVER_NAME = EasySwoole

MAIN_SERVER.HOST = 0.0.0.0
MAIN_SERVER.PORT = 9501
MAIN_SERVER.SERVER_TYPE = WEB_SERVER ## 可选为 SERVER  WEB_SERVER WEB_SOCKET_SERVER
MAIN_SERVER.SOCK_TYPE = SWOOLE_TCP  ## 该配置项当为SERVER_TYPE值为SERVER时有效
MAIN_SERVER.RUN_MODEL = SWOOLE_PROCESS

MAIN_SERVER.SETTING.task_worker_num = 8
MAIN_SERVER.SETTING.task_max_request = 500
MAIN_SERVER.SETTING.worker_num = 8
MAIN_SERVER.SETTING.max_request = 5000

TEMP_DIR = null
LOG_DIR = null

############## 这里是用户自己的配置 ##################
DATABASE.ip=127.0.0.1
DATABASE.port=3306
DATABASE.user=root
DATABASE.password=root

```

也可以新建php或者env文件进行添加配置,例如:  

添加App/Conf/web.php和App/Conf/app.env  

EasySwooleEvent.php文件写法示例:  


```php
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Utility\File;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        self::loadConf();
        // TODO: Implement initialize() method.
    }

    /**
     * 加载配置文件
     */
    public static function loadConf(){
        $files = File::scanDirectory(EASYSWOOLE_ROOT.'/Application/Config');
        if(is_array($files)){
            foreach ($files['files'] as $file) {
                $fileNameArr= explode('.',$file);
                $fileSuffix = end($fileNameArr);
                if($fileSuffix=='php'){
                    Config::getInstance()->loadFile($file);
                }elseif($fileSuffix=='env'){
                    Config::getInstance()->loadEnv($file);
                }
            }
        }
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data):void
    {

    }

}

```
## 生产与开发配置分离
在php easyswoole start命令下,默认为开发模式,加载dev.env  
运行 php easyswoole start produce 命令时,为生产模式,加载produce.env
 

## DI注入配置
es3.x提供了几个Di参数配置,可自定义配置脚本错误异常处理回调,控制器命名空间,最大解析层级等
```php
<?php
Di::getInstance()->set(SysConst::ERROR_HANDLER,function (){});//配置错误处理回调
Di::getInstance()->set(SysConst::SHUTDOWN_FUNCTION,function (){});//配置脚本结束回调
Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE,'App\\HttpController\\');//配置控制器命名空间
Di::getInstance()->set(SysConst::HTTP_CONTROLLER_MAX_DEPTH,5);//配置http控制器最大解析层级
Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,function (){});//配置http控制器异常回调
Di::getInstance()->set(SysConst::HTTP_CONTROLLER_POOL_MAX_NUM,15);//http控制器对象池最大数量
```

## 动态配置
当你在控制器(worker进程)中修改某一项配置时,由于进程隔离,修改的配置不会在其他进程生效,所以我们可以使用动态配置:  
动态配置将配置数据存储在swoole_table中,取/修改配置数据时是从swoole_table直接操作,所有进程都可以使用  
>但是不适合存储大量\大长度的的配置,建议用于开关存储等小数据型数据存储    

```php
Config::getInstance()->setDynamicConf('test_config_value', 0);//配置一个动态配置项
$test_config_value_1 = Config::getInstance()->getDynamicConf('test_config_value');//获取一个配置
Config::getInstance()->delDynamicConf('test_config_value');//删除一个配置
```
