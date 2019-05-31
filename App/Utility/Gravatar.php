<?php

namespace App\Utility;

/**
 * Gravatar
 * Class Gravatar
 * @package App\Utility
 */
class Gravatar
{
    /**
     * 生成一个Gravatar头像
     * @param string $email
     * @param int $size
     * @return string
     */
    public static function makeGravatar(string $email, int $size = 120)
    {
        $hash = md5($email);
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=identicon";
    }
}