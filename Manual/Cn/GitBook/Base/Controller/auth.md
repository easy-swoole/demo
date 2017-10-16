# 请求拦截与权限控制
EasySwoole支持在三个地方进行请求拦截，当一个HTTP请求进来，EasySwoole的执行先后顺序是：
- Event中的OnRequest事件
- 自定义路由（可选）
- 控制器中的OnRequest

> 在以上任意位置执行 $response->end(),均不会进入下一个流程。

## 使用例子
例如我现在 /Api/Mobile 下的全部控制器，均需要做权限控制，那么我们先建立一个全局的抽象控制器。
```
abstract class AbstractBase extends AbstractController
{
    protected $who;
    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
        $cookie = $this->request()->getCookieParams(SysConst::WX_USER_COOKIE_NAME);
        if(empty($cookie)){
            $this->response()->writeJson(Status::CODE_UNAUTHORIZED);
            $this->response()->end();
        }else{
            $info = Redis::getInstance()->getConnect()->hGet(SysConst::REDIS_USER_INFO,$cookie);
            if(is_array($info)){
                if(time() - $info['time']< SysConst::WX_USER_COOKIE_TTL){
                    $this->who = $info['userBean'];
                }else{
                    $this->response()->writeJson(Status::CODE_UNAUTHORIZED);
                    $this->response()->end();
                }
            }else{
                $this->response()->writeJson(Status::CODE_UNAUTHORIZED);
                $this->response()->end();
            }
        }
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFound() method.
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }
}
```


而后，我/Api/Mobile下的任意一个控制器，仅需继承该方法，即可实现权限控制。

```
namespace App\Controller\Api\Mobile;

class Index extends AbstractBase
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write($this->who->getOpenId());
    }
}
```

## 使用Session
EasySwoole也支持用户使用session。

> 注意：以上代码仅仅做逻辑展示，请勿直接使用。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>