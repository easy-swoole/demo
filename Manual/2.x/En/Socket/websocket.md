# WebSocket Controller

## Change Main Server Type
vim your Config.php,and change SERVER_TYPE item into 
```
\EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SOCKET_SERVER
```

## Implement You Package Parser
you must implement a package parser which implement 'EasySwoole\Core\Socket\AbstractInterface\ParserInterface',in case easyswoole can understand the command from websocket client and response the correct data-form to it. For example:

suppose the data-form of client is a json,
```
{
    'action'=>xxx,
    'content'=>[
    ]
}
```
so the parser is
```php
namespace App;


use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

class Parser implements ParserInterface
{

    public static function decode($raw, $client)
    {
        // TODO: Implement decode() method.
        $command = new CommandBean();
        $json = json_decode($raw,1);
        //suppose all target controller is \App\WebSocket\Test::class
        $command->setControllerClass(\App\WebSocket\Test::class);
        $command->setAction($json['action']);
        $command->setArg('content',$json['content']);
        return $command;

    }
    
    // @$raw is a  string which create from SplStream what you response at your controller action
    public static function encode(string $raw, $client): ?string
    {
        // TODO: Implement encode() method.
        if(!empty($raw)){
            return $raw;
        }else{
            return null;
        }
    }
}
```

## Register OnMessage Callback
vim EasySwooleEvent.php
```php
use \EasySwoole\Core\Swoole\EventHelper;

public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // TODO: Implement mainServerCreate() method.
    EventHelper::registerDefaultOnMessage($register,\App\Parser::class);
}
```

 