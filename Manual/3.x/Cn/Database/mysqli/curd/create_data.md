## 数据对象创建  
mysqli组件没有自行封装数据对象,可通过继承EasySwoole工具类[splBean](../../../Component/Spl/bean.md)自行创建一个数据对象.

### 示例:
```php
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/5
 * Time: 10:09 AM
 */

namespace App\Model\User;


use EasySwoole\Spl\SplBean;

class UserBean extends SplBean
{
    protected $id;
    protected $account;
    protected $password;
    protected $nickName;
    protected $openId;
    protected $addTime;
    protected $lastLoginTime;
    protected $balance;
    protected $mobile;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * @param mixed $addTime
     */
    public function setAddTime($addTime): void
    {
        $this->addTime = $addTime;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     */
    public function setBalance($balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return mixed
     */
    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    /**
     * @param mixed $lastLoginTime
     */
    public function setLastLoginTime($lastLoginTime): void
    {
        $this->lastLoginTime = $lastLoginTime;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * @param mixed $nickName
     */
    public function setNickName($nickName): void
    {
        $this->nickName = $nickName;
    }

    /**
     * @return mixed
     */
    public function getOpenId()
    {
        return $this->openId;
    }

    /**
     * @param mixed $openId
     */
    public function setOpenId($openId): void
    {
        $this->openId = $openId;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = md5($password);
    }
}

```


### 调用:
```php
<?php
$user = new UserBean(['account'=>'tioncico']);
$user->setPassword(123456);
$user->setMobile('15000011000');
$account = $user->getAccount();
$user_array = $user->toArray(null, UserBean::FILTER_NOT_NULL);//转为数组并过滤掉null值
```
