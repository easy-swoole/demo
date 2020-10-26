<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\TcpController;

use EasySwoole\Socket\AbstractInterface\Controller;

abstract class Base extends Controller
{
    protected function actionNotFound(?string $actionName)
    {
        $this->response()->setMessage('not found!');
    }
}
