<?php

namespace App\Model\Admin;

use EasySwoole\ORM\AbstractModel;

/**
 * Class AdminModel
 * Create With Automatic Generator
 * @property $adminId
 * @property $adminName
 * @property $adminAccount
 * @property $adminPassword
 * @property $adminSession
 * @property $adminLastLoginTime
 * @property $adminLastLoginIp
 */
class AdminModel extends AbstractModel
{
    protected $tableName = 'admin_list';

    protected $primaryKey = 'adminId';


    /**
     * @getAll
     * @keyword adminName
     * @param  int  page  1
     * @param  string  keyword
     * @param  int  pageSize  10
     * @return array[total,list]
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['adminAccount'] = ['%' . $keyword . '%','like'];
        }
        $list = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    function login():?AdminModel
    {
        $info = $this->get(['adminAccount'=>$this->adminAccount,'adminPassword'=>$this->adminPassword]);
        return $info;
    }

    /*
     * 以account进行查询
     */
    function accountExist($field='*'):?AdminModel
    {
        $info = $this->field($field)->get(['adminAccount'=>$this->adminAccount]);
        return $info;
    }

    function getOneBySession($field='*'):?AdminModel
    {
        $info = $this->field($field)->get(['adminSession'=>$this->adminSession]);
        return $info;
    }

    function logout()
    {
        return $this->update(['adminSession'=>'']);
    }

}