# SplBean
SplBean是一个抽象类,借以实现类似Java Bean中半自动化ORM。
例如在java中常见的：
```
$db->insert('user_table',$bean->toArray());
```
当用户需要Bean层时，只需要新建对应的Bean class并继承\Core\Component\Spl\SplBean实现其initialize()。

```
class UserBean extends \Core\Component\Spl\SplBean {
    protected $account;
    protected $sex;
    protected $addTime;

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
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
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
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;
    }
    
    protected function initialize()
    {
        // TODO: Implement initialize() method.
        $this->addTime = time();
    }
}
```

## 使用
```
$bean = new UserBean(
    array(
        'account'=>"account",
        'sex'=>0,
        'other'=>'other'
    )
);
var_dump($bean->toArray());
array(3) {
  ["account"]=>
  string(7) "account"
  ["sex"]=>
  int(0)
  ["addTime"]=>
  int(1504024995)
}
```
> Bean对象在实例化时，可以选择性的传递一个数组作为Bean对象初始化参数，Bean对象会自动过滤无关的键值对。

## 注意事项
- 每个Bean必须实现initialize方法，该方法在Bean实例被创建时执行，用于为Bean某些成员变量做初始化设定。该方法中若对成员属性进行赋值，其优先级是最高的。
因此若需要保留在实例化对象时传入的属性值，请做判断再赋值。例如：
```
protected function initialize()
{
    // TODO: Implement initialize() method.
    if(emptye($this->addTime)){
        $this->addTime = time();
    }
}
```
- 成员变量请确保全部为protected，并实现其get/set方法。

```
$bean->setSex(null);
var_dump($bean->toArray(false,['account','sex']));
array(2) {
  ["account"]=>
  string(7) "account"
  ["sex"]=>
  NULL
}

var_dump($bean->toArray(UserBean::FILTER_TYPE_NOT_NULL,['account','sex']));
array(1) {
  ["account"]=>
  string(7) "account"
}

$bean->setSex('');
var_dump($bean->toArray(UserBean::FILTER_TYPE_NOT_EMPTY));
array(2) {
  ["account"]=>
  string(7) "account"
  ["addTime"]=>
  int(1504025589)
}
```
> 在将Bean对象转数组的时候，可以选择Bean对象中的指定字段进行导出，以及对字段值未NULL或者是为空的进行过滤。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>