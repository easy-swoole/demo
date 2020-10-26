<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

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
    public static function emailIsExist($email)
    {
        clearstatcache();
        $dir = self::getStorageDir();
        return is_file($dir . DIRECTORY_SEPARATOR . md5($email));
    }

    /**
     * 获取存储目录
     * @return string
     */
    public static function getStorageDir()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'User';
    }
}
