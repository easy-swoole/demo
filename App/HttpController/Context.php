<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2019/1/7 0007
 * Time: 15:26
 */
namespace App\HttpController;

class Context extends \EasySwoole\Http\AbstractInterface\Controller{
    function index()
    {
        $mysqlObject = \EasySwoole\Component\Context\ContextManager::getInstance()->get('mysqlObject');
        $data = ($mysqlObject->get('test'));
        $this->response()->write(json_encode($data));
        // TODO: Implement index() method.
    }


}