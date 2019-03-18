<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Mysqli\Mysqli;
use EasySwoole\Mysqli\Config as MysqlConfig;

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
    
    function checkmysql()
    {
        $conf = new MysqlConfig(Config::getInstance()->getConf('MYSQL'));
        $db = new Mysqli($conf);
        $data = $db->get('test');
        var_dump($data);
    }
}
