# Socket
EasySwoole support websocket\udp\tcp Controller .

## How It Realize

### Dispatcher 
Dispatcher is the key to how it works;
```
namespace EasySwoole\Core\Socket;

use EasySwoole\Core\Component\Invoker;
use EasySwoole\Core\Component\Spl\SplStream;
use EasySwoole\Core\Component\Trigger;
use EasySwoole\Core\Socket\AbstractInterface\ExceptionHandler;
use EasySwoole\Core\Socket\Client\Tcp;
use EasySwoole\Core\Socket\Client\Udp;
use EasySwoole\Core\Socket\Client\WebSocket;
use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;
use EasySwoole\Core\Swoole\ServerManager;

class Dispatcher
{
    const TCP = 1;
    const WEB_SOCK = 2;
    const UDP = 3;
    const PACKAGE_PARSER_ERROR = 'PACKAGE_PARSER_ERROR';
    const TARGET_CONTROLLER_NOT_FOUND = 'TARGET_CONTROLLER_NOT_FOUND';
    private $parser;
    private $exceptionHandler;
    private $errorHandler = null;
    
    /*
        when you create a sock dispatch ,the package parser is require
    */
    function __construct(string $parserInterface)
    {
        try{
            $ref = new \ReflectionClass($parserInterface);
            if($ref->implementsInterface(ParserInterface::class)){
                $this->parser = $parserInterface;
            }else{
                throw new \Exception("class {$parserInterface} not a implement ".'EasySwoole\Core\Socket\AbstractInterface\ParserInterface');
            }
        }catch (\Throwable $throwable){
            Trigger::throwable($throwable);
            throw $throwable;
        }
    }

    public function setExceptionHandler(string $handler = null):Dispatcher
    {
        if($handler == null){
            return $this;
        }
        try{
            $ref = new \ReflectionClass($handler);
            if($ref->implementsInterface(ExceptionHandler::class)){
                $this->exceptionHandler = $handler;
            }else{
                throw new \Exception("class {$handler} not a implement ".'EasySwoole\Core\Socket\AbstractInterface\ExceptionHandler');
            }
        }catch (\Throwable $throwable){
            Trigger::throwable($throwable);
            throw $throwable;
        }
        return $this;
    }

    public function setErrorHandler(callable $callable = null)
    {
        $this->errorHandler = $callable;
    }

    /*
     * $args:
     *  Tcp  $fd，$reactorId
     *  Web Socket swoole_websocket_frame $frame
     *  Udp array $client_info;
     */
    function dispatch($type ,string $data, ...$args):void
    {
        //create client 
        switch ($type){
            case self::TCP:{
                $client = new Tcp($args[0],$args[1]);
                break;
            }
            case self::WEB_SOCK:{
                $client = new WebSocket($args[0]);
                break;
            }
            case self::UDP:{
                $client = new Udp($args[0]);
                break;
            }
            default:{
                Trigger::error('dispatcher type error',__FILE__,__LINE__);
                return;
            }
        }
        //try to decode a command from socket data
        $command = null;
        try{
            $command = $this->parser::decode($data,$client);
        }catch (\Throwable $throwable){
            Trigger::throwable($throwable);
        }
        //if can not successed in parser the command ,do the error callback
        if($command === null){
            $this->hookError(self::PACKAGE_PARSER_ERROR,$data,$client);
            return;
        }else if($command instanceof CommandBean){
            //get the target controller class
            $controller = $command->getControllerClass();
            if(class_exists($controller)){
                try{
                    $response = new SplStream();
                    (new $controller($client,$command,$response));
                    $res = $this->parser::encode($response->__toString(),$client);
                    //response to client
                    if($res !== null){
                        Response::response($client,$res);
                    }
                }catch (\Throwable $throwable){
                    $this->hookException($throwable,$data,$client);
                }
            }else{
                $this->hookError(self::TARGET_CONTROLLER_NOT_FOUND,$data,$client);
            }
        }
    }

    private function hookError($status,string $raw,$client)
    {
        if(is_callable($this->errorHandler)){
            try{
                $ret = Invoker::callUserFunc($this->errorHandler,$status,$raw,$client);
                if(is_string($ret)){
                    $res = $this->parser::encode($ret,$client);
                    if($res !== null){
                        Response::response($client,$res);
                    }
                }
            }catch (\Throwable $exception){
                $this->hookException($exception,$raw,$client);
            }
        }else{
            //默认没有错误处理的时候，关闭连接
            $this->closeClient($client);
        }
    }

    private function closeClient($client)
    {
        if(!$client instanceof Udp){
            ServerManager::getInstance()->getServer()->close($client->getFd());
        }
    }

    private function hookException(\Throwable $throwable,string $raw,$client)
    {
        if(class_exists($this->exceptionHandler)){
            Try{
                $ret = $this->exceptionHandler::handler($throwable,$raw,$client);
                if(is_string($ret)){
                    $res = $this->parser::encode($ret,$client);
                    if($res !== null){
                        Response::response($client,$res);
                    }
                }
            }catch (\Throwable $throwable){
                Trigger::throwable($throwable);
                $this->closeClient($client);
            }
        }else{
            $this->closeClient($client);
        }
    }
}
```

### Callback Register 

EventHelper.php
```
namespace EasySwoole\Core\Swoole;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Event;
use EasySwoole\Core\Component\SuperClosure;
use EasySwoole\Core\Component\SysConst;
use EasySwoole\Core\Component\Trigger;
use EasySwoole\Core\Http\AbstractInterface\ExceptionHandlerInterface;
use EasySwoole\Core\Http\Dispatcher;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Socket\Dispatcher as SocketDispatcher;
use EasySwoole\Core\Swoole\PipeMessage\Message;
use EasySwoole\Core\Swoole\Task\AbstractAsyncTask;
use \EasySwoole\Core\Swoole\PipeMessage\EventRegister as PipeMessageEventRegister;

class EventHelper
{
    public static function registerDefaultOnReceive(EventRegister $register,string $parserInterface,callable $onError = null,string $exceptionHandler = null):void
    {
        $dispatch = new SocketDispatcher($parserInterface);
        $dispatch->setErrorHandler($onError);
        $dispatch->setExceptionHandler($exceptionHandler);
        $register->add($register::onReceive,function (\swoole_server $server, int $fd, int $reactor_id, string $data)use($dispatch){
            $dispatch->dispatch($dispatch::TCP,$data,$fd,$reactor_id);
        });
    }

    public static function registerDefaultOnPacket(EventRegister $register,string $parserInterface,callable $onError = null,string $exceptionHandler = null)
    {
        $dispatch = new SocketDispatcher($parserInterface);
        $dispatch->setErrorHandler($onError);
        $dispatch->setExceptionHandler($exceptionHandler);
        $register->set($register::onPacket,function (\swoole_server $server, string $data, array $client_info)use($dispatch){
            $dispatch->dispatch($dispatch::UDP,$data,$client_info);
        });
    }

    public static function registerDefaultOnMessage(EventRegister $register,string $parserInterface,callable $onError = null,string $exceptionHandler = null)
    {
        $dispatch = new SocketDispatcher($parserInterface);
        $dispatch->setErrorHandler($onError);
        $dispatch->setExceptionHandler($exceptionHandler);
        $register->set($register::onMessage,function (\swoole_server $server, \swoole_websocket_frame $frame)use($dispatch){
            $dispatch->dispatch($dispatch::WEB_SOCK,$frame->data,$frame);
        });
    }
}
```