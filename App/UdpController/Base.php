<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

namespace App\UdpController;

use EasySwoole\Socket\AbstractInterface\Controller;

abstract class Base extends Controller
{
    protected function actionNotFound(?string $actionName)
    {
        $this->response()->setMessage('not found!');
    }
}