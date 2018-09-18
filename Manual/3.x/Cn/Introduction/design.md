# 设计解读
EasySwoole 3 为全新的组件化设计，全携程。

## 代码阅读

```
/bin/easyswoole.php
```
easyswoole 3的入口管理脚本，依旧是easyswoole.php。我们以服务启动为说明，进行设计流程讲解。

### 核心类
EasySwoole 3 的核心类完整命名空间如下：
```
EasySwoole\EasySwoole\Core
```
它是一个单例类(use EasySwoole\Component\Singleton)，当需要启动服务，执行启动命令：
```
php easyswoole start
```
管理脚本做了以下几件事：
- 环境检查
- 实例化(单例模式)***EasySwoole\EasySwoole\Config***，并加载配置文件
- 实例化(单例模式)***Core***，执行***Core***中的 ***initialize*** 方法
- 执行***Core***中的***createServer***方法,并启动服务

***Core*** 类的方法列表:
- __construct
    
    在结构函数中，进行了两个常量定义:
    ```
    defined('SWOOLE_VERSION') or define('SWOOLE_VERSION',intval(phpversion('swoole')));
    defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT',realpath(getcwd()));
    ```
- initialize

    该方法用于框架的初始化(单元测试的时候可以配合使用)，该方法中，做了以下事情：
    * 检查并执行全局事件 ***EasySwooleEvent.php*** 中的***initialize*** 方法
    * 调用***Core***中的***sysDirectoryInit***方法对框架目录进行初始化
    * 调用***Core***中的***registerErrorHandler***方法注册框架的错误处理器
    
- sysDirectoryInit

    该方法的实现如下：
    ```
    //创建临时目录    请以绝对路径，不然守护模式运行会有问题
    $tempDir = Config::getInstance()->getConf('TEMP_DIR');
    if(empty($tempDir)){
        $tempDir = EASYSWOOLE_ROOT.'/Temp';
        Config::getInstance()->setConf('TEMP_DIR',$tempDir);
    }
    if(!is_dir($tempDir)){
        mkdir($tempDir);
    }
    
    $logDir = Config::getInstance()->getConf('LOG_DIR');
    if(empty($logDir)){
        $logDir = EASYSWOOLE_ROOT.'/Log';
        Config::getInstance()->setConf('LOG_DIR',$logDir);
    }
    if(!is_dir($logDir)){
        mkdir($logDir);
    }
    //设置默认文件目录值
    Config::getInstance()->setConf('MAIN_SERVER.SETTING.pid_file',$tempDir.'/pid.pid');
    Config::getInstance()->setConf('MAIN_SERVER.SETTING.log_file',$logDir.'/swoole.log');
    //设置默认日志处理器的记录目录
    //EasySwoole\EasySwoole\Logger
    Logger::getInstance($logDir);
    ```    
    因此，如果不想用默认的临时文件和日志目录，用户可以自己再配置项中修改
    
- registerErrorHandler

    在该方法中，注册了默认的两个错误处理：
    * set_error_handler
    * register_shutdown_function
    
    两个默认的错误处理中，触发错误的时候，均以触发：
    ```
    //EasySwoole\EasySwoole\Trigger
    Trigger::getInstance()->error($description,$l);
    ```
    的方法进行处理。
    
- createServer
    
    该方法的实现代码如下：
    ```
    //读取配置项
    $conf = Config::getInstance()->getConf('MAIN_SERVER');
    //实例化(单例)EasySwoole\EasySwoole\ServerManager,并实例化对应的swoole server
    ServerManager::getInstance()->createSwooleServer(
        $conf['PORT'],$conf['SERVER_TYPE'],$conf['HOST'],$conf['SETTING'],$conf['RUN_MODEL'],$conf['SOCK_TYPE']
    );
    //hook swoole server
    $this->mainServerHook($conf['SERVER_TYPE']);
    //执行全局事件 ***EasySwooleEvent.php*** 中的***mainServerCreate*** 方法
    EasySwooleEvent::mainServerCreate(ServerManager::getInstance()->getMainEventRegister());
    ```
    
-  mainServerHook

    该方法主要是用与注册默认的onReceive或者是onRequest事件。实现代码如下：
    ```
    if($type === ServerManager::TYPE_SERVER){
        //如果是纯swoole server 那么注册回调为全局事件 ***EasySwooleEvent.php*** 中的***onReceive*** 方法
        ServerManager::getInstance()->getSwooleServer()->on(EventRegister::onReceive,function (\swoole_server $server, int $fd, int $reactor_id, string $data){
            EasySwooleEvent::onReceive($server,$fd,$reactor_id,$data);
        });
    }else{
        //如果不是swoole server，那么就是swoole http server 或者是swoole websocket server，因此都有http请求回调
        
        //获取并注册Http控制器命名空间
        $namespace = Di::getInstance()->get(SysConst::HTTP_CONTROLLER_NAMESPACE);
        if(empty($namespace)){
            $namespace = 'App\\HttpController\\';
        }
        
        //获取并注册URL路径最大解析层级
        $depth = intval(Di::getInstance()->get(SysConst::HTTP_CONTROLLER_MAX_DEPTH));
        $depth = $depth > 5 ? $depth : 5;
        $max = intval(Di::getInstance()->get(SysConst::HTTP_CONTROLLER_POOL_MAX_NUM));
        if($max == 0){
            $max = 100;
        }
        //创建一个WebService ,用的 https://github.com/easy-swoole/http
        //use EasySwoole\Http\Request;
        // use EasySwoole\Http\Response;
        // use EasySwoole\Http\WebService;
        $webService = new WebService($namespace,Trigger::getInstance(),$depth,$max);
        
        //获取并注册全局的onRequest异常回调
        $httpExceptionHandler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
        if($httpExceptionHandler){
            $webService->setExceptionHandler($httpExceptionHandler);
        }
        
        //取得当前的server
        $server = ServerManager::getInstance()->getSwooleServer();
        
        //回调注册
        EventHelper::on($server,EventRegister::onRequest,function (\swoole_http_request $request,\swoole_http_response $response)use($webService){
            $request_psr = new Request($request);
            $response_psr = new Response($response);
            try{
                //执行全局事件 ***EasySwooleEvent.php*** 中的***onRequest*** 方法
                if(EasySwooleEvent::onRequest($request_psr,$response_psr)){
                    $webService->onRequest($request_psr,$response_psr);
                }
            }catch (\Throwable $throwable){
                Trigger::getInstance()->throwable($throwable);
            }finally{
                try{
                    //执行全局事件 ***EasySwooleEvent.php*** 中的***afterRequest*** 方法
                    EasySwooleEvent::afterRequest($request_psr,$response_psr);
                }catch (\Throwable $throwable){
                    Trigger::getIn=stance()->throwable($throwable);
                }
            }
        });
    }
    ```   
    
###  ServerManager 类

ServerManager 它是一个单例类(use EasySwoole\Component\Singleton),完整的命名空间如下：
```
EasySwoole\EasySwoole\ServerManager
```
方法列表如下：

- __construct
    
    构造函数中，实例化了一个***EasySwoole\EasySwoole\Swoole\EventRegister***，事件注册器其实就是一个事件容器。
    
- createSwooleServer
    
    创建一个主swoole实例，注意，不需要自己创建，EasySwoole已经帮你创建好了。
    
- addServer
    
    注册一个子服务，即为swoole addListen的封装实现。        