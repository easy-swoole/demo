<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 上午11:14
 */

namespace App\HttpController\Pool;


use App\HttpController\Base;
use App\Model\User\UserModelWithDb;
use EasySwoole\Http\Message\Status;

class MysqlWithDb extends Base
{
    function index() {
        $page = intval($this->request()->getRequestParam('page'));
        $page < 1 && $page = 1;
        $model = new UserModelWithDb();
        $users = $model->getAll($page);
        unset($model);
        $this->writeJson(Status::CODE_OK, $users, 'success');
    }
}