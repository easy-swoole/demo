# SplBean

SplBean是一个抽象类,借以实现类似Java Bean中半自动化ORM。 例如在java中常见的：

```php
$db->insert('user_table',$bean->toArray());
```

当用户需要Bean层时，只需要新建对应的Bean class并继承\EasySwoole\Spl\SplBean。

```php
class UserBean extends \EasySwoole\Spl\SplBean {
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

    protected function initialize(): void
    {
        // TODO: Implement initialize() method.
        $this->addTime = time();
    }
}

```

## 使用

```php
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

- 每个

  Bean

  可以实现initialize方法，该方法在

  Bean

  实例被创建时执行，用于为

  Bean

  某些成员变量做初始化设定。该方法中若对成员属性进行赋值，其优先级是最高的。 因此若需要保留在实例化对象时传入的属性值，请做判断再赋值。例如：

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

```php
$bean->setSex(null);
var_dump($bean->toArray(['account','sex']));
array(2) {
  ["account"]=>
  string(7) "account"
  ["sex"]=>
  NULL
}

var_dump($bean->toArray(['account','sex'], UserBean::FILTER_NOT_NULL));
array(1) {
  ["account"]=>
  string(7) "account"
}

$bean->setSex('');
var_dump($bean->toArray(null, UserBean::FILTER_NOT_EMPTY));
array(2) {
  ["account"]=>
  string(7) "account"
  ["addTime"]=>
  int(1504025589)
}

```

> 在将Bean对象转数组的时候，可以选择Bean对象中的指定字段进行导出，以及对字段值未NULL或者是为空的进行过滤。

## 方法介绍

```php
const FILTER_NOT_NULL = 1;// 过滤不为NULL的属性
const FILTER_NOT_EMPTY = 2;//0 过滤不为空的属性，不算empty
```

构造方法：

 `data` 为类属性集合，`autoCreateProperty` 是否自动创建不存在的属性

```php
public function __construct(array $data = null,$autoCreateProperty = false)
```

获得所有属性集合：

```php
final public function allProperty():array
```

转换为数组：

`columns` 键值集合，`filter` 过滤条件（FILTER_NOT_NULL | FILTER_NOT_EMPTY），

```php
function toArray(array $columns = null, $filter = null):array
```

 将数组转批量添加为bean规范的属性：

 `data` 为类属性集合，`autoCreateProperty` 是否自动创建不存在的属性

```php
final public function arrayToBean(array $data,$autoCreateProperty = false):SplBean
```

添加属性：

`name ` 属性名字，`value` 属性值

```php
final public function addProperty($name,$value = null):void
```

获得属性：

`name` 属性名字

```php
final public function getProperty($name)
```

指定需要被序列化成 JSON 的数据：

由于Bean类继承了JsonSerializable接口，遵循规范需要实现jsonSerialize方法。

```php
final public function jsonSerialize():array
```

初始化方法：

由于遵循Bean类的规范，构造方法的参数被固定了，如果需要预处理，可以采用该方法进行处理。

```php
protected function initialize():void
```

将类属性名和值的集合转换成字符串：

返回的是一个规范的json对象。

```php
public function __toString()
```

重置类成员变量值：

```php
public function restore(array $data = [])
```
