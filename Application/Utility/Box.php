<?php

namespace App\Utility;

/**
 * Class Box
 * @author  : evalor <master@evalor.cn>
 * @package App\Utility
 */
class Box
{
    private static $instance;

    private $items = [];  // 储存对象

    /**
     * 获取本类的实例或者直接取出一个对象
     * @author : evalor <master@evalor.cn>
     * @param string     $name  对象名称 只传入名称则为取出对象
     * @param null|mixed $value 传入值则为存放对象
     * @return Box|mixed|null
     */
    static function instance($name = '', $value = null)
    {
        if (!isset(self::$instance)) self::$instance = new self;
        if ($name === '') return self::$instance;

        if ($value !== null) {
            return self::$instance->item($name, $value);
        } else {
            return self::$instance->item($name);
        }
    }

    /**
     * 存取对象
     * @param string $name
     * @param mixed  $value
     * @author : evalor <master@evalor.cn>
     * @return bool|mixed|null
     */
    function item($name, $value = null)
    {
        $hashName = md5($name);
        $itemIns = isset($this->items[$hashName]) ? $this->items[$hashName] : null;
        if ($value !== null) {
            $this->items[$hashName] = $value;
            return true;
        } else {
            return $itemIns;
        }
    }
}