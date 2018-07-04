# 控制器
## URL解析规则
内置路由支持无限层级的路由,即Controller可以无限嵌套目录,如:

http://127.0.0.1:9501/api/auth/login

执行的方法为:\App\Controller\Api\Auth::login()

http://127.0.0.1:9501/a/b/c/d/f

如f为控制器名,执行的方法为:\App\Controller\A\B\C\D\F::index()
如F为方法名,执行的方法为:\App\Controllers\A\B\C\D::f()

## 示例代码
例如分别建立App\Controller\Api\Auth与App\Controller\Api\Index控制器。
```
namespace App\Controller\Api;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;

class Auth extends AbstractController
{

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("api auth index");
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
    function login(){
        /*
         * url is /api/auth/login/index.html
         */
        $this->response()->writeJson(Status::CODE_OK,null,'this is auth login ');
    }
}
```

```
namespace App\Controller\Api;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use Core\Http\Message\UploadFile;

class Index extends AbstractController
{

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("this is api index");/*  url:domain/api/index.html  domain/api/  */
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFount() method.
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterResponse()
    {
        // TODO: Implement afterResponse() method.
    }
    function api(){
        $this->response()->write("this is api api");
    }
}
```
若想访问Auth::index,则URL为ip:port/api/auth/index.html


