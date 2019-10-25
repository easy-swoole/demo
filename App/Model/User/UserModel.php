<?php

namespace App\Model\User;

use EasySwoole\ORM\AbstractModel;

/**
 * Class UserModel
 * Create With Automatic Generator
 * @property $userId
 * @property $userName
 * @property $userAccount
 * @property $userPassword
 * @property $phone
 * @property $money
 * @property $addTime
 * @property $lastLoginIp
 * @property $lastLoginTime
 * @property $userSession
 * @property $state
 */
class UserModel extends AbstractModel
{
    protected $tableName = 'user_list';

    protected $primaryKey = 'userId';

    const STATE_PROHIBIT = 0;//禁用状态
    const STATE_NORMAL = 1;//正常状态

    /**
     * @getAll
     * @keyword userName
     * @param  int  page  1
     * @param  string  keyword
     * @param  int  pageSize  10
     * @return array[total,list]
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['userAccount'] = ['%' . $keyword . '%','like'];
        }
        $list = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }


    public function getOneByPhone($field='*'): ?UserModel
    {
        $info = $this->field($field)->get(['phone'=>$this->phone]);
        return $info;
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    function login():?UserModel
    {
        $info = $this->get(['userAccount'=>$this->userAccount,'userPassword'=>$this->userPassword]);
        return $info;
    }

    function getOneBySession($field='*'):?UserModel
    {
        $info = $this->field($field)->get(['userSession'=>$this->userSession]);
        return $info;
    }

    function logout(){
        return $this->update(['userSession'=>'']);
    }

}
