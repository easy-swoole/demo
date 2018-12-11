<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午1:20
 */

namespace App\HttpController;

use App\Rpc\RpcServer;
use EasySwoole\EasySwoole\FastCache\Cache;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Bean\Response;

class FastCache extends Controller
{
    /*
     * 具体使用看https://github.com/easy-swoole/rpc/
     */
    function index()
    {
        $this->response()->withHeader('Content-type','application/json;charset=utf-8');
        $cache = Cache::getInstance();
        $cache->set('name','仙士可');
        $cache->set('啥都是大','仙士可');
        $cache->set('都懂得','仙士可');
        $cache->set('name2','仙士可2号');
        sleep(1);
        $keys = $cache->keys();
        var_dump($keys);
        $str = "现在存储的数据有:";
        foreach ($keys as $key) {
            $value = $cache->get($key);
            $str .= "$key:$value\n";
        }
        $this->response()->write($str);

    }

    function set()
    {
        $data = $this->request()->getRequestParam();
        $cache = Cache::getInstance();
        $cache->set($data['key'],$data['value']);
        $this->response()->write('缓存成功');
    }
}