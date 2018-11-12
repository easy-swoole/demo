#EasySwoole 验证码组件  
EasySwoole提供了独立的验证码组件,地址:https://github.com/easy-swoole/verifyCode,几行代码即可实现输出一个验证码
## composer 安装
```
composer require easyswoole/verifycode
```

### 示例:  
```php
<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/12 0012
 * Time: 16:30
 */

namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\VerifyCode\Conf;

class VerifyCode extends Controller
{
    function index()
    {
        $config = new Conf();
        $code = new \EasySwoole\VerifyCode\VerifyCode($config);
        $this->response()->withHeader('Content-Type','image/png');
        $this->response()->write($code->DrawCode()->getImageByte());
    }

    function getBase64(){
        $config = new Conf();
        $code = new \EasySwoole\VerifyCode\VerifyCode($config);
        $this->response()->write($code->DrawCode()->getImageBase64());
    }
}
```