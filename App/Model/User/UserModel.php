<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:51
 */

namespace App\Model\User;


use App\Model\BaseModel;

class UserModel extends BaseModel
{
    protected $table = 'user';

    /*
     * 获取列表数据
     */
    function getAll(int $page = 1, int $pageSize = 10) {
        $data = $this->getDbConnection()->withTotalCount()->orderBy('id', 'DESC')->get($this->table, [($page - 1) * $pageSize, $page * $pageSize]);
        $total = $this->getDbConnection()->getTotalCount();
        return ['data' => $data, 'total' => $total];
    }

    /*
     * 获取用户详情
     */
    function getOne(UserBean $userBean):?UserBean {
        $data = $this->getDbConnection()->where('id', $userBean->getId())->getOne($this->table);
        return empty($data) ? null : new UserBean($data);
    }

    /*
     * 修改用户信息
     */
    function update(UserBean $userBean, $data):bool {
        $result = $this->getDbConnection()->where('id', $userBean->getId())->update($this->table, $data);
        return $result;
    }
}