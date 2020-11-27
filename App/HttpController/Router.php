<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\HttpAnnotation\Utility\Scanner;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
//        $this->setGlobalMode(true);
//        $this->setGlobalMode(false);
//        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
//            $response->write('未找到处理方法');
//        });
//        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
//            $response->write('未找到路由匹配');
//        });
        // TODO: Implement initialize() method.
        $routeCollector->get('/user', '/Test/user');
        $routeCollector->get('/test', '/Index/test');


        // 将http-annotation api注解的path注入路由
        $scanner = new Scanner();
        $scanner->mappingRouter($routeCollector, EASYSWOOLE_ROOT . '/App/HttpController', 'App\HttpController');
    }
}
