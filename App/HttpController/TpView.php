<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/1 0001
 * Time: 14:19
 */

namespace App\HttpController;


use App\HttpController\TpViewController;

class TpView extends TpViewController
{
    function index()
    {
        $this->assign('content','this is TpView content.');
        $this->fetch('view');
//        $this->response()->write(1);
        // TODO: Implement index() method.
    }

}