<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 上午11:41
 */

namespace App\HttpController;


use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use FastRoute\RouteCollector;

class Router extends \EasySwoole\Core\Http\AbstractInterface\Router
{
    function register(RouteCollector $routeCollector)
    {
        // TODO: Implement register() method.
        //路由拦截若未执行end，请求还会继续进入系统自带的控制器路由匹配


        //测试URL  /a
        $routeCollector->get('/a',function (Request $request,Response $response){
            $response->write('this is write by router with not end ');
        });

        //测试URL  /a2
        $routeCollector->get('/a2',function (Request $request,Response $response){
            $response->write('this is write by router2 with end ');
            $response->end();
        });

        // /user/1/index.html
        $routeCollector->get( '/user/{id:\d+}',function (Request $request ,Response $response,$id){
            $response->write("this is router user ,your id is {$id}");
            $response->end();
        });

        //传递给 /index控制器 test2方法
        $routeCollector->get( '/user2/{id:\d+}','/test2');
    }

}