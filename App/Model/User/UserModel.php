<?php

namespace App\Model\User;

/**
 * Class UserModel
 * Create With Automatic Generator
 */
class UserModel extends \App\Model\BaseModel
{
    protected $table = 'user_list';

    protected $primaryKey = 'userId';


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
        if (!empty($keyword)) {
            $this->getDbConnection()->where('userAccount', '%' . $keyword . '%', 'like');
        }

        $list = $this->getDbConnection()
            ->withTotalCount()
            ->orderBy($this->primaryKey, 'DESC')
            ->get($this->table, [$pageSize * ($page  - 1), $pageSize]);
        $total = $this->getDbConnection()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }


    /**
     * 默认根据主键(userId)进行搜索
     * @getOne
     * @param  UserBean $bean
     * @return UserBean
     */
    public function getOne(UserBean $bean,$field='*'): ?UserBean
    {
        $info = $this->getDbConnection()->where($this->primaryKey, $bean->getUserId())->getOne($this->table,$field);
        if (empty($info)) {
            return null;
        }
        return new UserBean($info);
    }

    public function getOneByPhone($phone,$field='*'): ?UserBean
    {
        $info = $this->getDbConnection()->where('phone', $phone)->getOne($this->table,$field);
        if (empty($info)) {
            return null;
        }
        return new UserBean($info);
    }


    /**
     * 默认根据bean数据进行插入数据
     * @add
     * @param  UserBean $bean
     * @return bool
     */
    public function add(UserBean $bean): bool
    {
        return $this->getDbConnection()->insert($this->table, $bean->toArray(null, $bean::FILTER_NOT_NULL));
    }


    /**
     * 默认根据主键(userId)进行删除
     * @delete
     * @param  UserBean $bean
     * @return bool
     */
    public function delete(UserBean $bean): bool
    {
        return  $this->getDbConnection()->where($this->primaryKey, $bean->getUserId())->delete($this->table);
    }


    /**
     * 默认根据主键(userId)进行更新
     * @delete
     * @param  UserBean $bean
     * @param  array    $data
     * @return bool
     */
    public function update(UserBean $bean, array $data): bool
    {
        if (empty($data)){
            return false;
        }
        return $this->getDbConnection()->where($this->primaryKey, $bean->getUserId())->update($this->table, $data);
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    function login(UserBean $userBean): ?UserBean
    {
        $user = $this->getDbConnection()
            ->where('userAccount', $userBean->getUserAccount())
            ->where('userPassword', $userBean->getUserPassword())
            ->getOne($this->table);
        if (empty($user)) {
            return null;
        }
        return new UserBean($user);
    }


    function getOneBySession($session)
    {
        $user = $this->getDbConnection()
            ->where('userSession', $session)
            ->getOne($this->table);
        if (empty($user)) {
            return null;
        }
        return new UserBean($user);
    }

    function logout(UserBean $bean){
        $update = [
            'userSession'=>'',
        ];
        return $this->getDbConnection()->where($this->primaryKey, $bean->getUserId())->update($this->table, $update);
    }

}
