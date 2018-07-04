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
