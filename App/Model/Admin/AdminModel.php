<?php

namespace App\Model\Admin;

/**
 * Class AdminModel
 * Create With Automatic Generator
 */
class AdminModel extends \App\Model\BaseModel
{
    protected $table = 'admin_list';

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
        if (!empty($keyword)) {
            $this->getDbConnection()->where('adminAccount', '%' . $keyword . '%', 'like');
        }

        $list = $this->getDbConnection()
            ->withTotalCount()
            ->orderBy($this->primaryKey, 'DESC')
            ->get($this->table, [$pageSize * ($page - 1), $pageSize]);
        $total = $this->getDbConnection()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }


    /**
     * 默认根据主键(adminId)进行搜索
     * @getOne
     * @param  AdminBean $bean
     * @return AdminBean
     */
    public function getOne(AdminBean $bean): ?AdminBean
    {
        $info = $this->getDbConnection()->where($this->primaryKey, $bean->getAdminId())->getOne($this->table);
        if (empty($info)) {
            return null;
        }
        return new AdminBean($info);
    }


    /**
     * 默认根据bean数据进行插入数据
     * @add
     * @param  AdminBean $bean
     * @return bool
     */
    public function add(AdminBean $bean): bool
    {
        return $this->getDbConnection()->insert($this->table, $bean->toArray(null, $bean::FILTER_NOT_NULL));
    }


    /**
     * 默认根据主键(adminId)进行删除
     * @delete
     * @param  AdminBean $bean
     * @return bool
     */
    public function delete(AdminBean $bean): bool
    {
        return $this->getDbConnection()->where($this->primaryKey, $bean->getAdminId())->delete($this->table);
    }


    /**
     * 默认根据主键(adminId)进行更新
     * @delete
     * @param  AdminBean $bean
     * @param  array     $data
     * @return bool
     */
    public function update(AdminBean $bean, array $data): bool
    {
        if (empty($data)) {
            return false;
        }
        return $this->getDbConnection()->where($this->primaryKey, $bean->getAdminId())->update($this->table, $data);
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    function login(AdminBean $userBean): ?AdminBean
    {
        $user = $this->getDbConnection()
            ->where('adminAccount', $userBean->getAdminAccount())
            ->where('adminPassword', $userBean->getAdminPassword())
            ->getOne($this->table);
        if (empty($user)) {
            return null;
        }
        return new AdminBean($user);
    }

    /*
     * 以account进行查询
     */
    function accountExist(AdminBean $userBean): ?AdminBean
    {
        $user = $this->getDbConnection()
            ->where('adminAccount', $userBean->getAdminAccount())
            ->getOne($this->table);
        if (empty($user)) {
            return null;
        }
        return new AdminBean($user);
    }

    function getOneBySession($session)
    {
        $user = $this->getDbConnection()
            ->where('adminSession', $session)
            ->getOne($this->table);
        if (empty($user)) {
            return null;
        }
        return new AdminBean($user);
    }

    function logout(AdminBean $bean){
        $update = [
            'adminSession'=>'',
        ];
        return $this->getDbConnection()->where($this->primaryKey, $bean->getAdminId())->update($this->table, $update);
    }

}