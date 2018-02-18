## 异常处理

示例：

```php
<?php

namespace EasySwoole;

use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\EventRegister;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\AbstractInterface\EventInterface;


class EasySwooleEvent implements EventInterface
{
	public function frameInitialize() : void
	{
		Di::getInstance()->set( SysConst::HTTP_EXCEPTION_HANDLER, \App\ExceptionHandler::class );
	}
    .....
}
```

> \App\ExceptionHandler 文件

```php
<?php
namespace App;

use EasySwoole\Core\Http\AbstractInterface\ExceptionHandlerInterface;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

class ExceptionHandler implements ExceptionHandlerInterface
{
	public function handle( \Throwable $exception, Request $request, Response $response )
	{
		var_dump($exception);
	}
}
```

> 试试在控制器里，随便实例化一个不存在的类，如：new B() 让其报错下试试。
>
> 注意：长连接时请做好异常处理，因为在mysql断开时会抛出错误中断运行，这时捕获下重新连接再执行sql语句。

相关资源

Whoops to easySwoole framework 2.x  https://github.com/easy-swoole/easyWhoops

