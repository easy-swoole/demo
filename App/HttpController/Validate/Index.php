<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-14
 * Time: 下午2:33
 */

namespace App\HttpController\Validate;


use App\HttpController\Base;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

class Index extends Base
{
    function index() {
        $validate = new Validate();
        $validate->addColumn('name')->required('姓名必填');
        $validate->addColumn('age')->required('年龄必填')->between(20, 30, '年龄只能在20岁到30岁之前');
        if ($this->validate($validate)) {
            $this->writeJson(Status::CODE_OK, null, 'success');
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, $validate->getError()->__toString(), 'fail');
        }
    }
}