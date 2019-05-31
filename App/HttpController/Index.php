<?php

namespace App\HttpController;

use App\Utility\ReverseProxyTools;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;

/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Base
{
    function index()
    {
        $hostName = $this->cfgValue('WEBSOCKET_HOST', 'ws://127.0.0.1:9501');
        $this->render('index', [
            'server' => $hostName
        ]);
    }
}
