<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-12-11
 * Time: 下午2:24
 */

namespace App\HttpController\Advanced;


use App\HttpController\Base;
use EasySwoole\EasySwoole\Swoole\Memory\AtomicManager;

class Atomic extends Base
{
    function index() {
        $atomic = AtomicManager::getInstance()->get('second');
        $atomic->add(1);
        $this->response()->write($atomic->get());
    }
}