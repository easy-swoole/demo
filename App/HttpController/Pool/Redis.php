<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:40
 */

namespace App\HttpController\Pool;


use App\HttpController\BaseWithRedis;

class Redis extends BaseWithRedis
{
    function getName() {
        $this->getRedis()->set('name', 'blank');
        $name = $this->getRedis()->get('name');
        $this->response()->write($name);
    }
}