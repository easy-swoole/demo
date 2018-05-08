# Router
EasySwoole support custom router,the default implement is base on [fastRoute](https://github.com/nikic/FastRoute).

## Create You Router
create Router class in 'App\HttpController' ,and implement '\EasySwoole\Core\Http\AbstractInterface\Router';for example:

```
namespace App\HttpController;


use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use FastRoute\RouteCollector;

class Router extends \EasySwoole\Core\Http\AbstractInterface\Router
{

    function register(RouteCollector $routeCollector)
    {
        // TODO: Implement register() method.
        // will continue url match 
        $routeCollector->get('/',function (Request $request ,Response $response){
            $response->write('this router index');
        });
        // would not continue url match 
        $routeCollector->get('/test',function (Request $request ,Response $response){
            $response->write('this router test');
            $response->end();
        });
        
        $routeCollector->get( '/user/{id:\d+}',function (Request $request ,Response $response,$id){
            $response->write("this is router user ,your id is {$id}");
            $response->end();
        });

        // parser router args and continue url match with args
        $routeCollector->get( '/user2/{id:\d+}','/test2');

    }
}
```
