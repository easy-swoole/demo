<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/5
 * Time: 上午12:00
 */

namespace App\Model\User;


use App\Model\BaseOne;
use App\Utility\TrackerManager;
use EasySwoole\Utility\Random;

class UserModelOne extends BaseOne
{
    protected $table = 'user_list';

    function register(UserBean $bean)
    {
        return $this->getDb()->insert($this->table,$bean->toArray());
    }

    function delete(UserBean $bean)
    {
        return $this->getDb()->where('userId',$bean->getUserId())->delete($this->table);
    }

    function update(UserBean $bean,array $data)
    {
        return $this->getDb()->where('userId',$bean->getUserId())->update($this->table,$data);
    }

    function updateByAccount(UserBean $bean,array $data)
    {
        return $this->getDb()->where('account',$bean->getAccount())->update($this->table,$data);
    }

    function login(UserBean $bean):?UserBean
    {
        $info = $this->getDb()->where('userId',$bean->getUserId())
            ->where('password',$bean->getPassword())->get($this->table);
        if(empty($info)){
            $session = md5(time().Random::character(6));
            $this->updateByAccount($bean,[
                'session'=>$session
            ]);
            $bean->setSession($session);
            return $bean;
        }else{
            return null;
        }
    }

    function sessionExist(UserBean $bean):?UserBean
    {
        $data = $this->getDb()->where('session',$bean->getSession())->getOne($this->table);
        if($data){
            return new UserBean($data);
        }else{
            return null;
        }
    }

    function all()
    {
        $caller = TrackerManager::getInstance()->getTracker()->addCaller('allUser',null,'DB');
        $ret =  $this->getDb()->get($this->table);
        $caller->endCall($caller::STATUS_SUCCESS,[
            'sql'=>$this->getDb()->getLastQuery(),
        ]);
        return $ret;
    }
}