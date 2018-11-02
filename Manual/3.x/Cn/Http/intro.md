# HTTP 组件
EasySwoole 3采用了独立组件的形式，将各个小模块拆分。HTTP独立组件地址：https://github.com/easy-swoole/http  
demo地址[HttpController](https://github.com/easy-swoole/demo/tree/3.x/App/HttpController)

## 独立测试代码
### HTTP.php
```php
$trigger = new \EasySwoole\Trace\Trigger();

$http = new swoole_http_server("0.0.0.0", 9501);

$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

//default controller namespace is  App\HttpController

$service = new \EasySwoole\Http\WebService($controllerNameSpace = 'App\\HttpController\\',$trigger,$depth = 5);
$service->setExceptionHandler(function (\Throwable $throwable,\EasySwoole\Http\Request $request,\EasySwoole\Http\Response $response){
    $response->write('error');
});

$http->on("request", function ($request, $response)use($service) {
    $req = new \EasySwoole\Http\Request($request);
    $resp = new \EasySwoole\Http\Response($response);
    $service->onRequest($req,$resp);
});

$http->start();

```

### Index.php
```php
namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method
        $this->response()->write('hello world');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->response()->write("{$action} not found");
    }

    function testSession()
    {
        $this->session()->start();
        $this->session()->set('a',1);
        $this->session()->writeClose();
    }

    function testSession2()
    {
        $this->session()->start();
        $this->response()->write($this->session()->get('a'));
    }

    function testException()
    {
        new NoneClass();
    }

    protected function onException(\Throwable $throwable): void
    {
        $this->response()->write($throwable->getMessage());
    }

    public function gc()
    {
        var_dump('class :'.static::class.' is recycle to pool');
    }
}
```