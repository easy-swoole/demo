<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-05
 * Time: 20:51
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Spl\SplBean;

/**
 * model 1写法控制器
 * Class Index
 * @package App\HttpController
 */
class Index extends Controller
{

    function index()
    {
        $redis = Redis::defer('redis');
        $redis->set('name','仙士可');
        $this->response()->write(($redis->get('name')));
        // TODO: Implement index() method.
    }

}