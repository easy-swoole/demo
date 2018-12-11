<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:01
 */

namespace App\HttpController\Pool;


use App\Model\User\UserBean;
use App\Model\User\UserModel;
use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\Exception\PoolEmpty;
use EasySwoole\Component\Pool\Exception\PoolUnRegister;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Mysql2 extends Controller
{

    function index()
    {
        try {
            MysqlPool::invoke(function (MysqlObject $mysqlObject) {
                $model = new UserModel($mysqlObject);
                $model->insert(new UserBean($this->request()->getRequestParam()));
            });
        } catch (\Throwable $throwable) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, $throwable->getMessage());
        }

        $this->writeJson(Status::CODE_OK, null, 'success');
    }

}