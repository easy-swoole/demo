# 自定义错误处理
EasySwoole支持用户自定义error handler
## 创建错误处理器
实现ErrorHandlerInterface接口
```
namespace App;

use Core\AbstractInterface\ErrorHandlerInterface;

class Test implements ErrorHandlerInterface
{

    function handler( $msg,$file = null,$line = null,$errorCode = null,$trace )
    {
        echo "文件{$file}的第{$line}行，错误：{$msg}";
    }
    
    function display($msg,$file = null,$line = null,$errorCode = null,$trace )
    {
    }
    
    function log( $msg,$file = null,$line = null,$errorCode = null,$trace )
    {
    }

}
```
> 当开启DEBUG.ENABLE的时候，则自定义错误处理有效。

## IOC注入
在框架初始化后事件注入：
```
Di::getInstance()->set(SysConst::ERROR_HANDLER,Test::class);
```

> 注意，若在接下去的beforeWorker事件中有逻辑错误，则会导致在服务启动前，错误处理类立即被实例化，
若在处理函数内，有用到例如redis等连接，则会造成多进程连接共用问题，为避免该情况，可以利用task进程去转换。
例如，发生错误信息的时候，则投递至task进程，在task进程中去获取一个单例的redis连接，来写入错误信息。


