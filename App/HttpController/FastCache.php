<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午1:20
 */

namespace App\HttpController;

use App\Rpc\RpcServer;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Bean\Response;

class FastCache extends Controller
{
    /*
     * 具体使用看https://github.com/easy-swoole/rpc/
     */
    function index()
    {
        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        $cache = Cache::getInstance();
//        $cache->set('name', '仙士可');//设置
        $keys = $cache->keys();
        $str = "现在存储的数据有:";
        foreach ($keys as $key) {
            $value = $cache->get($key);
            $str .= "$key:$value\n";
        }
        $this->response()->write($str);
    }

    function allMethod()
    {
        $cache = Cache::getInstance();
        $cache->set('name', '仙士可');//设置
        $cache->get('name');//获取
        $cache->keys();//获取所有key
        $cache->unset('name');//删除key
        $cache->flush();//清空所有key
        ($cache->enQueue('listA', '1'));//增加一个队列数据
        ($cache->enQueue('listA', '2'));//增加一个队列数据
        ($cache->enQueue('listA', '3'));//增加一个队列数据
        var_dump($cache->queueSize('listA'));//队列大小
        var_dump($cache->queueList('listA'));//队列大小
//      var_dump(  $cache->unsetQueue('listA');//删除队列
//        var_dump($cache->queueList('listA'));//队列列表
        var_dump($cache->flushQueue());//清空队列
        var_dump($cache->deQueue('listA'));//出列
        var_dump($cache->deQueue('listA'));//出列
    }

    function set()
    {
        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        $data = $this->request()->getRequestParam();
        $cache = Cache::getInstance();
        $cache->set($data['key'], $data['value']);
        $this->response()->write('缓存成功');
    }

    function enQueue(){
        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        $data = $this->request()->getRequestParam();
        $cache = Cache::getInstance();
        $cache->enQueue($data['key'], $data['value']);
        $this->response()->write('入列成功');
    }

    function queueList(){

        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        $cache = Cache::getInstance();
        $list = $cache->queueList();
        $str = "现在存储的队列有:";
        foreach ($list as $queue) {
            $size = $cache->queueSize($queue);
            $str .= "$queue:$size\n";
        }
        $this->response()->write($str);
    }
}