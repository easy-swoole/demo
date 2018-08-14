<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/15
 * Time: 上午12:01
 */

namespace App\HttpController;


use App\Utility\TrackerManager;

class Trace extends Base
{
    function index()
    {
        /*
         * 可以写进去model中
         */
        TrackerManager::getInstance()->getTracker()->addAttribute('user','用户名1');
        TrackerManager::getInstance()->getTracker()->addAttribute('token','用户名token');
        //设置追踪1
        $caller = TrackerManager::getInstance()->getTracker()->addCaller('CurlBaiDu','wd=easyswoole');
        file_get_contents('https://www.baidu.com/s?wd=easyswoole');
        $caller->endCall();
        //设置追踪2，模拟失败任务
        $caller = TrackerManager::getInstance()->getTracker()->addCaller('CurlBaiDu2','wd=easyswoole');
        file_get_contents('https://www.baidu.com/s?wd=easyswoole');
        $caller->endCall($caller::STATUS_FAIL,'curl失败了');


        $this->response()->write('call trace');

    }

    function afterAction(?string $actionName): void
    {
        //每次请求后，都结束当前协程的追踪
        TrackerManager::getInstance()->closeTracker();
    }
}