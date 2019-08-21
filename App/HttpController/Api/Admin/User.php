<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/26
 * Time: 5:39 PM
 */

namespace App\HttpController\Api\Admin;

use App\Model\User\UserBean;
use App\Model\User\UserModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Validate\Validate;

class User extends AdminBase
{
    function getAll()
    {
        $db = Mysql::defer('mysql');
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $model = new UserModel($db);
        $data = $model->getAll($page, $this->input('keyword'), $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }


    function getOne()
    {
        $db = Mysql::defer('mysql');
        $param = $this->request()->getRequestParam();
        $data['userId'] = intval($param['userId']);
        $model = new UserModel($db);
        $rs = $model->getOne(new UserBean($data));
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }

    }


    function add()
    {
        $db = Mysql::defer('mysql');
        $param = $this->request()->getRequestParam();
        $model = new UserModel($db);
        $bean = new UserBean($param);
        $bean->setUserPassword(md5($param['userPassword']));
        $bean->setState($this->input('state',1));
        $bean->setMoney(0);
        $bean->setAddTime(time());
        $rs = $model->add($bean);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $db->getLastError());
        }
    }

    function update()
    {
        $db = Mysql::defer('mysql');
        $model = new UserModel($db);
        $userInfo = $model->getOne( new UserBean(['userId' => $this->input('userId')]));
        if (!$userInfo){
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '未找到该会员');
        }
        $password = $this->input('userPassword');
        $updateBean = new UserBean();
        $updateBean->setUserName($this->input('userName',$userInfo->getUserName()));
        $updateBean->setUserPassword($password?md5($password):$userInfo->getUserPassword());
        $updateBean->setState($this->input('state',$userInfo->getState()));
        $updateBean->setPhone($this->input('phone',$userInfo->getPhone()));

        $rs = $model->update($userInfo, $updateBean->toArray([], $updateBean::FILTER_NOT_EMPTY));
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $db->getLastError());
        }

    }

    function delete()
    {
        $db = Mysql::defer('mysql');
        $param = $this->request()->getRequestParam();
        $model = new UserModel($db);
        $bean = new UserBean(['userId' => intval($param['userId'])]);
        $rs = $model->delete($bean);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '删除失败');
        }

    }

    function getValidateRule(?string $action): ?Validate
    {
        $validate = null;
        switch ($action) {
            case 'getAll':
                $validate = new Validate();
                $validate->addColumn('page','页数')->optional();
                $validate->addColumn('limit','limit')->optional();
                $validate->addColumn('keyword','关键词')->optional();
                break;
            case 'getOne':
                $validate = new Validate();
                $validate->addColumn('userId', '会员id')->required()->lengthMax(11);
                break;
            case 'add':
                $validate = new Validate();
                $validate->addColumn('userName', '会员名')->required()->lengthMax(18);
                $validate->addColumn('userAccount', '会员账号')->required()->lengthMax(32);
                $validate->addColumn('userPassword', '会员密码')->required()->lengthMax(18);
                $validate->addColumn('phone', '手机号')->optional()->lengthMax(18);
                $validate->addColumn('state', '状态')->optional()->inArray([0,1]);
                break;
            case 'update':
                $validate = new Validate();
                $validate->addColumn('userId', '会员id')->required()->lengthMax(11);
                $validate->addColumn('userName', '会员名')->optional()->lengthMax(18);
                $validate->addColumn('userPassword', '会员密码')->optional()->lengthMax(18);
                $validate->addColumn('phone', '手机号')->optional()->lengthMax(18);
                $validate->addColumn('state', '状态')->optional()->inArray([0,1]);
                break;
            case 'delete':
                $validate = new Validate();
                $validate->addColumn('userId', '会员id')->required()->lengthMax(11);
                break;
        }
        return $validate;
    }
}