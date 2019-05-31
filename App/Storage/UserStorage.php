<?php

namespace App\Storage;

/**
 * 用户信息存储器
 * Class UserStorage
 * @package App\Storage
 */
class UserStorage
{
    /**
     * 邮箱是否存在
     * @param $email
     * @return bool
     */
    static function emailIsExist($email)
    {
        clearstatcache();
        $dir = self::getStorageDir();
        return is_file($dir . DIRECTORY_SEPARATOR . md5($email));
    }

    /**
     * 获取存储目录
     * @return string
     */
    static function getStorageDir()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'User';
    }
}