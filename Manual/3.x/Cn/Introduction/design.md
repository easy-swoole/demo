# 设计解读
EasySwoole 3 为全新的组件化设计，全协程。

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
- 判断命令参数是否存在produce,如果存在则
作为生产模式启动
- 实例化(单例模式)***Core***，执行***Core***中的 ***initialize*** 方法
- 实例化(单例模式)***Config***，并加载配置文件(根据是否为生产模式引入不同的配置文件)
- 执行***Core***中的***createServer***方法,并启动服务

***Core*** 类的方法列表:
- __construct  
   实现代码如下:
  ```php
    <?php
    function __construct()
    {
        defined('SWOOLE_VERSION') or define('SWOOLE_VERSION',intval(phpversion('swoole')));
        defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT',realpath(getcwd()));
    }
  ```
  在该函数中,定义了swoole版本以及框架目录
  

- setIsDev  
  实现代码如下:  
  ```php
  <?php
    function setIsDev(bool $isDev)
    {
        $this->isDev = $isDev;
        return $this;
    }
  ```
   该函数用于设置框架运行模式(开发/生产),不同模式加载的配置文件不同
- initialize  
  实现代码如下:
  ```php
  <?php
    function initialize()
    {
        //检查全局文件是否存在.
        $file = EASYSWOOLE_ROOT . '/EasySwooleEvent.php';
        if(file_exists($file)){
            require_once $file;
            try{
                $ref = new \ReflectionClass('EasySwoole\EasySwoole\EasySwooleEvent');
                if(!$ref->implementsInterface(Event::class)){
                    die('global file for EasySwooleEvent is not compatible for EasySwoole\EasySwoole\EasySwooleEvent');
                }
                unset($ref);
            }catch (\Throwable $throwable){
                die($throwable->getMessage());
            }
        }else{
            die('global event file missing');
        }
        //执行框架初始化事件
        EasySwooleEvent::initialize();
        //加载配置文件
        $this->loadEnv();
        //临时文件和Log目录初始化
        $this->sysDirectoryInit();
        //注册错误回调
        $this->registerErrorHandler();
        return $this;
    }
  ```
    该方法用于框架的初始化(单元测试的时候可以配合使用)，该方法中，做了以下事情：
    * 检查并执行全局事件 ***EasySwooleEvent.php*** 中的***initialize*** 方法
    * 调用***Core***中的***loadEnv***方法加载配置文件
    * 调用***Core***中的***sysDirectoryInit***方法对框架目录进行初始化
    * 调用***Core***中的***registerErrorHandler***方法注册框架的错误处理器

- createServer  
   实现代码如下:
   ```php
   <?php
    function createServer()
    {
        $conf = Config::getInstance()->getConf('MAIN_SERVER');
        ServerManager::getInstance()->createSwooleServer(
            $conf['PORT'],$conf['SERVER_TYPE'],$conf['HOST'],$conf['SETTING'],$conf['RUN_MODEL'],$conf['SOCK_TYPE']
        );
        $this->registerDefaultCallBack(ServerManager::getInstance()->getSwooleServer(),$conf['SERVER_TYPE']);
        EasySwooleEvent::mainServerCreate(ServerManager::getInstance()->getMainEventRegister());
        return $this;
    }
   ```
   该方法中，做了以下事情：  
    * 获取配置
    * 创建swooleServer服务
    * 注册服务回调事件
    * 执行***EasySwooleEvent***中的***mainServerCreate***事件

- start  
   实现代码如下:  
   ```php
   <?php
   function start()
       {
           //给主进程也命名
           if(PHP_OS != 'Darwin'){
               $name = Config::getInstance()->getConf('SERVER_NAME');
               cli_set_process_title($name);
           }
           (new TcpService(Config::getInstance()->getConf('CONSOLE')));
           ServerManager::getInstance()->start();
       }
   ```
    该方法中,做了以下事情: 
     * 主进程命名
     * 实例化一个tcp服务用于做控制台服务
     * swoole主服务启动

- sysDirectoryInit  
    实现代码如下:
    ```php
    <?php
    private function sysDirectoryInit():void
        {
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
            //设置目录
            Logger::getInstance($logDir);
        }
    ```
    该方法中,做了以下事情: 
     * 创建临时目录
     * 创建日志目录
     * 设置pid文件,swoole.log文件目录

- registerErrorHandler  
   实现代码如下: 
   ```php
   <?php
  private function registerErrorHandler()
    {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        $userHandler = Di::getInstance()->get(SysConst::ERROR_HANDLER);
        if(!is_callable($userHandler)){
            $userHandler = function($errorCode, $description, $file = null, $line = null){
                $l = new Location();
                $l->setFile($file);
                $l->setLine($line);
                Trigger::getInstance()->error($description,$l);
            };
        }
        set_error_handler($userHandler);

        $func = Di::getInstance()->get(SysConst::SHUTDOWN_FUNCTION);
        if(!is_callable($func)){
            $func = function (){
                $error = error_get_last();
                if(!empty($error)){
                    $l = new Location();
                    $l->setFile($error['file']);
                    $l->setLine($error['line']);
                    Trigger::getInstance()->error($error['message'],$l);
                }
            };
        }
        register_shutdown_function($func);
    }
   ```
    该方法中,做了以下事情: 
     * 开启显示错误,配置错误显示级别
     * 获取/设置置  错误处理回调函数
     * 获取/设置    脚本终止回调函数
        
- registerDefaultCallBack  
   实现代码如下:
   ```php
   <?php
    private function registerDefaultCallBack(\swoole_server $server,string $serverType)
    {
        //如果主服务仅仅是swoole server，那么设置默认onReceive为全局的onReceive
        if($serverType === ServerManager::TYPE_SERVER){
            $server->on(EventRegister::onReceive,function (\swoole_server $server, int $fd, int $reactor_id, string $data){
                EasySwooleEvent::onReceive($server,$fd,$reactor_id,$data);
            });
        }else{
            //命名空间
            $namespace = Di::getInstance()->get(SysConst::HTTP_CONTROLLER_NAMESPACE);
            if(empty($namespace)){
                $namespace = 'App\\HttpController\\';
            }
            //url解析最大层级,默认5
            $depth = intval(Di::getInstance()->get(SysConst::HTTP_CONTROLLER_MAX_DEPTH));
            $depth = $depth > 5 ? $depth : 5;
            //对象池控制器实例最大数,默认100
            $max = intval(Di::getInstance()->get(SysConst::HTTP_CONTROLLER_POOL_MAX_NUM));
            if($max == 0){
                $max = 100;
            }
            //实例化webService处理http服务
            $webService = new WebService($namespace,$depth,$max);
            $httpExceptionHandler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
            //获取并注册全局的onRequest异常回调
            if($httpExceptionHandler){
                $webService->setExceptionHandler($httpExceptionHandler);
            }
            EventHelper::on($server,EventRegister::onRequest,function (\swoole_http_request $request,\swoole_http_response $response)use($webService){
                $request_psr = new Request($request);
                $response_psr = new Response($response);
                try{
                    //先调用全局事件,如果返回true才进行http调度
                    if(EasySwooleEvent::onRequest($request_psr,$response_psr)){
                        $webService->onRequest($request_psr,$response_psr);
                    }
                }catch (\Throwable $throwable){
                    Trigger::getInstance()->throwable($throwable);
                }finally{
                    try{
                        EasySwooleEvent::afterRequest($request_psr,$response_psr);
                    }catch (\Throwable $throwable){
                        Trigger::getInstance()->throwable($throwable);
                    }
                }
            });
        }
        //注册默认的on task,finish  不经过 event register。因为on task需要返回值。不建议重写onTask,否则es自带的异步任务事件失效
        EventHelper::on($server,EventRegister::onTask,function (\swoole_server $server, $taskId, $fromWorkerId,$taskObj){
            if(is_string($taskObj) && class_exists($taskObj)){
                $taskObj = new $taskObj;
            }
            if($taskObj instanceof AbstractAsyncTask){
                try{
                    $ret =  $taskObj->run($taskObj->getData(),$taskId,$fromWorkerId);
                    //在有return或者设置了结果的时候  说明需要执行结束回调
                    $ret = is_null($ret) ? $taskObj->getResult() : $ret;
                    if(!is_null($ret)){
                        $taskObj->setResult($ret);
                        return $taskObj;
                    }
                }catch (\Throwable $throwable){
                    $taskObj->onException($throwable);
                }
            }else if($taskObj instanceof SuperClosure){
                try{
                    return $taskObj( $server, $taskId, $fromWorkerId);
                }catch (\Throwable $throwable){
                    Trigger::getInstance()->throwable($throwable);
                }
            }
            return null;
        });
        EventHelper::on($server,EventRegister::onFinish,function (\swoole_server $server, $taskId, $taskObj){
            //finish 在仅仅对AbstractAsyncTask做处理，其余处理无意义。
            if($taskObj instanceof AbstractAsyncTask){
                try{
                    $taskObj->finish($taskObj->getResult(),$taskId);
                }catch (\Throwable $throwable){
                    $taskObj->onException($throwable);
                }
            }
        });

        //注册默认的pipe通讯
        OnCommand::getInstance()->set('TASK',function ($fromId,$taskObj){
            if(is_string($taskObj) && class_exists($taskObj)){
                $taskObj = new $taskObj;
            }
            if($taskObj instanceof AbstractAsyncTask){
                try{
                    $taskObj->run($taskObj->getData(),ServerManager::getInstance()->getSwooleServer()->worker_id,$fromId);
                }catch (\Throwable $throwable){
                    $taskObj->onException($throwable);
                }
            }else if($taskObj instanceof SuperClosure){
                try{
                    $taskObj();
                }catch (\Throwable $throwable){
                    Trigger::getInstance()->throwable($throwable);
                }
            }
        });

        EventHelper::on($server,EventRegister::onPipeMessage,function (\swoole_server $server,$fromWorkerId,$data){
            $message = \swoole_serialize::unpack($data);
            if($message instanceof Message){
                OnCommand::getInstance()->hook($message->getCommand(),$fromWorkerId,$message->getData());
            }else{
                Trigger::getInstance()->error("data :{$data} not packet by swoole_serialize or not a Message Instance");
            }
        });

        //注册默认的worker start
        EventHelper::registerWithAdd(ServerManager::getInstance()->getMainEventRegister(),EventRegister::onWorkerStart,function (\swoole_server $server,$workerId){
            if(PHP_OS != 'Darwin'){
                $name = Config::getInstance()->getConf('SERVER_NAME');
                if( ($workerId < Config::getInstance()->getConf('MAIN_SERVER.SETTING.worker_num')) && $workerId >= 0){
                    $type = 'Worker';
                }else{
                    $type = 'TaskWorker';
                }
                cli_set_process_title("{$name}.{$type}.{$workerId}");
            }
        });
    }
   ```
    该方法中,做了以下事情: 
     * 如果主服务为swoole server,则只注册onReceive全局事件为回调函数
     * 如果主服务不是swoole server则:注册http服务的 onRequest回调,以及拦截异常
     * 注册onTask,onFinish回调
     * 注册默认的pipe通讯
     * 注册默认的worker start
     
- loadEnv  
    实现代码如下:
    ```php
    <?php
    private function loadEnv()
    {
        if($this->isDev){
            $file  = EASYSWOOLE_ROOT.'/dev.env';
        }else{
            $file  = EASYSWOOLE_ROOT.'/produce.env';
        }
        Config::getInstance()->loadEnv($file);
    }
    ```
    该方法判断了是否为开发环境,如果是,则加载dev.env配置文件,否则加载produce.env配置文件

    
###  ServerManager 类

ServerManager 它是一个单例类(use EasySwoole\Component\Singleton),完整的命名空间如下：
```
EasySwoole\EasySwoole\ServerManager
```
方法列表如下：

- __construct  

    构造函数中，实例化了一个***EasySwoole\EasySwoole\Swoole\EventRegister***，事件注册器其实就是一个事件容器。
- getSwooleServer  
    获取swoole server对象或者子服务
- createSwooleServer  
    创建swoole server
- addServer  
    注册一个子服务
- getMainEventRegister  
    获取事件注册器对象
- start  
    注册服务回调以及注册子服务,并启动
- attachListener  
    添加注册好的子服务到swoole server
- getSubServerRegister  
    获取已经注册好的子服务


- __construct
    
    构造函数中，实例化了一个***EasySwoole\EasySwoole\Swoole\EventRegister***，事件注册器其实就是一个事件容器。
    
- createSwooleServer
    
    创建一个主swoole实例，注意，不需要自己创建，EasySwoole已经帮你创建好了。
    
- addServer
    
    注册一个子服务，即为swoole addListen的封装实现。        