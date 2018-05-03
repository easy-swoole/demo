# 设计流程
本章将为大家讲解，EasySwoole的主体设计思路。
## 入口文件
EasySwoole\Core\Core

Core类是一个单例对象，在整个EasySwoole生命周期中，Core对象只会被实例化一次，在实例化的时候，就定义了EASYSWOOLE_ROOT这个全局宏。
```
defined('EASYSWOOLE_ROOT') or define("EASYSWOOLE_ROOT",realpath(getcwd()));
```
### Core类中的initialize方法
在该方法中，先后执行了：
- Di容器中SysConst::VERSION值的定义
- Di容器中SysConst::HTTP_CONTROLLER_MAX_DEPTH值的定义
- 创建全局事件容器（EasySwoole\Core\Component\Event），并将EasySwooleEvent.php中的事件加载到容器中
- 设置系统默认目录DIR_TEMP、DIR_LOG
- 执行全局事件容器中的frameInitialize事件
- 注册系统中的set_error_handler、register_shutdown_function

### Core类中的run方法
- 创建一个ServerManager实例并调用ServerManager的start方法，正式启动整个服务。

## 服务管理类
EasySwoole\Core\Swoole\ServerManager

ServerManager类是一个单例对象，在整个EasySwoole生命周期中，ServerManager对象只会被实例化一次。

### ServerManager类中的start方法
在该方法中先后执行了：
- 主服务的创建（createMainServer方法）
- 子服务\多端口监听（attachListener方法）
- 全局跨进程Cache的注册
- 集群模式的注册
- 服务正式启动（swoole server start）

### ServerManager中的createMainServer方法
该方法用于创建一个主服务。在该方法中做的事情如下：
- 读取Config.php中的配置并创建一个swoole server
- 创建主服务的事件注册器（EasySwoole\Core\Swoole\EventRegister），并通过调用EventHelper注册默认的onWorkerStart、OnTask、OnFinish、OnPipeMessage、OnRequest事件到注册器中
- 执行全局事件容器中的mainServerCreate事件
- 将主服务的事件注册器中的全部注册事件，绑定到主服务的回调事件中国。

### ServerManager中的attachListener方法
该方法主要是用于，将用户添加的子服务通过swoole server的addlistener方法做真实绑定并设置对应的子服务回调。

### ServerManager中的addServer方法
该方法主要是用于添加一个子服务，注册的时候并不会真实的调用swoole server的addlistener方法，而是临时存储在一个数组中。
注册的自服务，会返回一个EventRegister。该EventRegister只会此子服务有效。

-----------------------------------

## EasySwoole中EventHelper注册的默认事件
EasySwoole\Core\Swoole\EventHelper

### onRequest
```
public static function registerDefaultOnRequest(EventRegister $register,$controllerNameSpace = 'App\\HttpController\\'):void
    {
        $dispatcher = new Dispatcher($controllerNameSpace);
        $register->set($register::onRequest,function (\swoole_http_request $request,\swoole_http_response $response)use($dispatcher){
            $request_psr = new Request($request);
            $response_psr = new Response($response);
            try{
                $event = Event::getInstance();
                $event->hook('onRequest',$request_psr,$response_psr);
                $dispatcher->dispatch($request_psr,$response_psr);
                $event->hook('afterAction',$request_psr,$response_psr);
            }catch (\Throwable $throwable){
                $handler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
                if($handler instanceof ExceptionHandlerInterface){
                    $handler->handle($throwable,$request_psr,$response_psr);
                }else{
                    $response_psr->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
                    $response_psr->write(nl2br($throwable->getMessage() ."\n". $throwable->getTraceAsString()));
                }
            }
            $response_psr->response();
            $response_psr->end(true);
        });
    }
```
在EasySwoole默认的HTTP注册器方法中，先创建一个Dispatcher，并把该Dispatcher实例注册进事件回调中，回调执行的事件有：
- 把\swoole_http_request、swoole_http_response转化为PSR7的Request与response对象。
- 执行全局事件容器中的onRequest事件
- 对请求执行dispatch。
- 执行全局事件容器中的afterAction事件。
- 执行内容响应（Swoole http server write 与end)

