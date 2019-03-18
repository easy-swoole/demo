<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 22:22
 */

namespace App\HttpController\Api;


use App\HttpController\AbstractBase;

class Common extends AbstractBase
{
    /*
     * 用于客户端与服务端同步时间
     */
    function time()
    {
        return $this->writeJson(200,time());
    }
}