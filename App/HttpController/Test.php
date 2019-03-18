<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/18 0018
 * Time: 9:40
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class Test extends Controller
{
    function index()
    {
        $this->response()->write('test 控制器');
        // TODO: Implement index() method.
    }
}