# 版本控制
Easyswoole 提供了高自由度的版本控制插件，版本控制的代码实现主要文件均在Core\Component\Version目录中;
而版本控制的核心关键点在于对onRequest事件进行全局拦截，再做版本鉴定和请求重新分发。

## 使用
首先，在App目录下建立Version目录，并在目录内建立如下示例Version类文件，该类主要进行版本设置等。

```php
<?php
namespace App\Version;

use Core\Component\Version\AbstractRegister;
use Core\Component\Version\VersionList;
use Core\Http\Request;
use Core\Http\Response;

class Version extends AbstractRegister
{
    function register(VersionList $versionList)
    {
        // 对v2版本的信息进行设置，验证字段为version（请求时必带version => 版本号）

        $v2 = $versionList->add('v2', function () {
            if (Request::getInstance()->getRequestParam('version') == '2') {
                return true;
            } else {
                return false;
            }
        });
        
        // 设置路径等信息同自定义路由功能一致
        $v2->register()->addRoute(['GET', 'POST'], '/version', function () {
            Response::getInstance()->write('this is test 1');
            Response::getInstance()->end();
        });

        $v2->register()->addRoute(['GET', 'POST'], '/version/test', function () {
            Response::getInstance()->write('this is test 2');
            Response::getInstance()->end();
        });
    }
}
```
###其中 ：
在设置完以上版本控制规则后，在Event的OnRequest事件中开启版本处理。
```php
    
    function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.f
        Controller::getInstance(Version::class)->startController();
    }
```
> 版本控制会先找到当前匹配version设置的回调结果进行处理，如果既不是路径字符串，也不是闭包，再找 control 实例的defaulthandler，也没有设置默认的再找 control 实例的defaulthandler，最后走dispatch直接解析 url 。


