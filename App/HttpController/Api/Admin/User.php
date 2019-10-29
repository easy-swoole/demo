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
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

class User extends AdminBase
{
    /**
     * getAll
     * @Param(name="page", alias="页数", optional="", integer="")
     * @Param(name="limit", alias="每页总数", optional="", integer="")
     * @Param(name="keyword", alias="关键字", optional="", lengthMax="32")
     * @author Tioncico
     * Time: 14:01
     */
    function getAll()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $model = new UserModel();
        $data = $model->getAll($page, $this->input('keyword'), $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }


    /**
     * getOne
     * @Param(name="userId", alias="用户id", required="", integer="")
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     * @author Tioncico
     * Time: 11:48
     */
    function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $rs = $model->get($param['userId']);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }

    }

    /**
     * add
     * @Param(name="userName", alias="用户昵称", optional="", lengthMax="32")
     * @Param(name="userAccount", alias="用户名", required="", lengthMax="32")
     * @Param(name="userPassword", alias="用户密码", required="", lengthMin="6",lengthMax="18")
     * @Param(name="phone", alias="手机号码", optional="", lengthMax="18",numeric="")
     * @Param(name="state", alias="用户状态", optional="", inArray="{0,1}")
     * @author Tioncico
     * Time: 11:48
     */
    function add()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel($param);
        $model->userPassword = md5($param['userPassword']);
        $rs = $model->save();
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
        }
    }

    /**
     * update
     * @Param(name="userId", alias="用户id", required="", integer="")
     * @Param(name="userPassword", alias="会员密码", optional="", lengthMin="6",lengthMax="18")
     * @Param(name="userName", alias="会员名", optional="",  lengthMax="32")
     * @Param(name="state", alias="状态", optional="", inArray="{0,1}")
     * @Param(name="phone", alias="手机号", optional="",  lengthMax="18")
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     * @author Tioncico
     * Time: 11:54
     */
    function update()
    {
        $model = new UserModel();
        /**
         * @var $userInfo UserModel
         */
        $userInfo = $model->get($this->input('userId'));
        if (!$userInfo) {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '未找到该会员');
        }
        $password = $this->input('userPassword');
        $update = [
          'userName'=>$this->input('userName', $userInfo->userName),
          'userPassword'=>$password ? md5($password) : $userInfo->userPassword,
          'state'=>$this->input('state', $userInfo->state),
          'phone'=>$this->input('phone', $userInfo->phone),
        ];

        $rs = $model->update($update);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
        }

    }

    /**
     * delete
     * @Param(name="userId", alias="用户id", required="", integer="")
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     * @author Tioncico
     * Time: 14:02
     */
    function delete()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $rs = $model->destroy($param['userId']);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '删除失败');
        }

    }
}