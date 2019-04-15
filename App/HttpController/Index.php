<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/15 0015
 * Time: 14:05
 */

namespace App\HttpController;


use EasySwoole\Validate\Validate;

class Index extends Base
{
    protected function getValidateRule(?string $action): ?Validate
    {
        $validate = null;
        switch ($action) {
            case 'index';
                break;
            case 'test':
                $validate = new Validate();
                $validate->addColumn('name', '姓名')->required()->lengthMax(64);
                $validate->addColumn('age', '年龄')->required()->min(0)->max(100);
                break;

        }
        return $validate;

    }

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }

    function test(){
        $this->response()->write('验证成功');
    }


}