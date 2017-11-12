# 请求拦截与权限控制
EasySwoole支持在三个地方进行请求拦截，当一个HTTP请求进来，EasySwoole的执行先后顺序是：
- Event中的OnRequest事件
- 自定义路由（可选）
- 控制器中的OnRequest

> 在以上任意位置执行 $response->end(),均不会进入下一个流程。

## 权限验证拦截
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

## 全局请求安全过滤拦截
例如，项目上线后，由于前期开发未注意安全问题，没有做参数过滤，那么，可以在Event中的OnRequest事件，进行全局的请求参数过滤或者拦截。

建立测试拦截类
```
namespace App\Utility;


class Security
{
    /*
     * 本注入脚本从网上流传的360 php防注入代码改版  仅供做参考
     */
    private $getFilter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    private $postFilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    private $cookieFilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
    function check(array $data){
        foreach ($data as $item){
            if (preg_match("/".$this->getFilter."/is",$item) == 1){
                return true;
            }
            if (preg_match("/".$this->postFilter."/is",$item) == 1){
                return true;
            }
            if (preg_match("/".$this->cookieFilter."/is",$item) == 1){
                return true;
            }
        }
        return false;
    }

}
```
在onRequest事件中调用
```
function onRequest(Request $request, Response $response)
{
        // TODO: Implement onRequest() method.
        $sec = new Security();
        if($sec->check($request->getRequestParam())){
            $response->write("do not attack");
            $response->end();
            return;
        }
        if($sec->check($request->getCookieParams())){
            $response->write("do not attack");
            $response->end();
            return;
        }
}
```

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
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>
