<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:19
 */

namespace App\HttpController\Api;


class Common extends AbstractBase
{
    function one()
    {
        $this->response()->write('this is api common one');
    }

    function two()
    {
        $this->response()->write('this is api common two');
    }
}