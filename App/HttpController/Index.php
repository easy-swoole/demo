<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;


/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Controller
{
    function index()
    {
        $server = Config::getInstance()->getConf('SYSTEM.WS_SERVER_PATH');
        $vars = ['server' => rtrim($server, '/') . '/'];
        ob_start();
        extract($vars);
        include dirname(__FILE__) . '/../Views/index.php';
        $content = ob_get_clean();
        $this->response()->write($content);
    }
}
