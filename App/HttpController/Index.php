<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Session\Session;

class Index extends Controller
{

    public function index()
    {
        $param = $this->request()->getRequestParam();
        foreach ($param as $key=>$value){
            Session::getInstance()->set($key,$value);
        }

        $this->response()->write("当前session:".json_encode(Session::getInstance()->all()));
    }

    function test()
    {
        $this->response()->write('this is test');
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }
}
