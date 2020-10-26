<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

/**
 * 登录系统
 * Class Register
 * @package App\HttpController
 */
class Login extends Base
{
    public function index()
    {
        $this->render('login');
    }
}
