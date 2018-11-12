<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-12
 * Time: 上午9:34
 */

namespace App\HttpController\Log;


use App\HttpController\Base;
use EasySwoole\EasySwoole\Logger;

class Index extends Base
{
    function index()
    {
        Logger::getInstance()->log('hello world....');
        $this->response()->write('log....');
    }
}