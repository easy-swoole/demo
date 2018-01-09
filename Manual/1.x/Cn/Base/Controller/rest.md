# RESTful
easySwoole支持REST风格开发。在实现上，其实是对AbstractController进行了REST规则封装，本质上，也是一个控制器。
支持GET、POST、PUT、PATCH、DELETE、HEAD、OPTIONS。
## 实例代码
```
namespace App\Controller\Rest;


use Core\AbstractInterface\AbstractREST;
use Core\Http\Message\Status;

class Index extends AbstractREST
{
    function GETIndex(){
        $this->response()->write("this is REST GET Index");
    }
    function POSTIndex(){
        $this->response()->write("this is REST POST Index");
    }

    function GETTest(){
        $this->response()->write("this is REST GET test");
    }
    function POSTTest(){
        $this->response()->write("this is REST POST test");
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
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
> 所有的action均为请求方法+实际方法名。注意方法名为大驼峰法。

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
   