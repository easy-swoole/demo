<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Template\Render;

class Index extends Controller
{

    function index()
    {
        $this->response()->write(Render::getInstance()->render('index.tpl',[
            'user'=>'easyswoole',
            'time'=>time()
        ]));
    }
}