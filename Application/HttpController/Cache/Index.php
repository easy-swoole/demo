<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午12:25
 */

namespace App\HttpController\Cache;


use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $this->actionNotFound('index');
    }

    function set()
    {
        $time = microtime(true);
        Cache::getInstance()->set('test',time());
        $time = round(microtime(true)-$time,3);
        $this->response()->write('set action take '.$time);
    }

    function get()
    {
        $time = microtime(true);
        $data = Cache::getInstance()->get('test');
        $time = round(microtime(true)-$time,3);
        $this->response()->write('set action take '.$time." and data is".$data);
    }

    function push()
    {
        $time = microtime(true);
        Cache::getInstance()->enQueue('que',time());
        $time = round(microtime(true)-$time,3);
        $this->response()->write('enQueue action take '.$time);
    }

    function lpop()
    {
        $time = microtime(true);
        $data = Cache::getInstance()->deQueue('que');
        $time = round(microtime(true)-$time,3);
        $this->response()->write('deQueue action take '.$time." and data is".$data);
    }
}