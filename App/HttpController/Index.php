<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;

use EasySwoole\Component\Pool\PoolManager;
use App\Utility\Pool\MysqlPool;

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
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        $data = $db->get('a');
        
        //使用完毕需要回收
        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
        $this->response()->write(json_encode($data));
    }
}
