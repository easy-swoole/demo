<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-27
 * Time: 下午2:01
 */

// 加载库文件
require_once __DIR__."/../vendor/autoload.php";

class UserBean extends \EasySwoole\Spl\SplBean {

    protected $id;
    protected $name;

    // 设置字段别名映射
    function setKeyMapping(): array
    {
        return [
            'id' => 'userId',
            'name' => 'userName'
        ];
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}

$userBean = new UserBean(['id' => 1, 'name' => 'blank', 'age' => 12]);

var_dump($userBean->toArray());