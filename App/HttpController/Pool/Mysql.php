<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:01
 */

namespace App\HttpController\Pool;


use App\HttpController\BaseWithDb;
use App\Model\User\UserBean;
use App\Model\User\UserModel;
use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\Exception\PoolEmpty;
use EasySwoole\Component\Pool\Exception\PoolUnRegister;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Http\Message\Status;

class Mysql extends BaseWithDb
{
    function getUserList()
    {
        $page = intval($this->request()->getRequestParam('page'));
        $page < 1 && $page = 1;
        $model = new UserModel($this->getDbConnection());
        $data = $model->getAll($page);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    function getOneUser()
    {
        $params = $this->request()->getRequestParam();
        if (isset($params['id'])) {
            $bean = new UserBean($params);
            $model = new UserModel($this->getDbConnection());
            $result = $model->getOne($bean);
            if ($result) {
                $this->writeJson(Status::CODE_OK, $result, 'success');
            } else {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, '用户不存在');
            }
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, 'id不能为空');
        }
    }

    function index()
    {
//        $data = MysqlPool::invoke(function (MysqlObject $mysqlObject) {
//            $data = ($mysqlObject->get('test'));
//            return $data;
//        });
//        $this->response()->write(json_encode($data));
        $mysqlObject = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();

        $data = ($mysqlObject->get('test'));
        $this->response()->write(json_encode($data));


    }
}