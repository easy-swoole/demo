# Global Event
EasySwoole has four global event. all of the events is located in EasySwooleEvent.php which is in your project root.

## frameInitialize
when this function was called,EasySwoole has done this :
- define EASYSWOOLE_ROOT
- Log/Temp initialize

what you can do :
you might change some php.ini setting,or change default set_error_handler\register_shutdown_function callback etc.

## mainServerCreate
```php
@param \EasySwoole\Core\Swoole\ServerManager $server
@param \EasySwoole\Core\Swoole\EventRegister $register
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
}
```
when this function was called,EasySwoole has done this :
- global frameInitialize event
- create main swoole server and register default callback

what you can do:
- register main server callback
- add a swoole sub listener
- add a custom swoole process

## onRequest
```php
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;
public static function onRequest(Request $request,Response $response): ?bool
```
onRequest method will be called at each http request .

## afterAction