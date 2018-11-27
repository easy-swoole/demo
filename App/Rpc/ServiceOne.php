<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/23 0023
 * Time: 14:44
 */

namespace App\Rpc;



class ServiceOne extends AbstractService
{
    function a1(){
        $this->getResponse()->setMessage('测试方法');
    }
}