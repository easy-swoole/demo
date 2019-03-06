<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/6 0006
 * Time: 14:46
 */

namespace App\HttpController;

use App\Model\ConditionBean;
use App\Model\Member\MemberModel;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Spl\SplBean;

/**
 * 控制器获取数据库连接的第二种写法
 * Class Index2
 * @package App\HttpController
 */
class Mysql3 extends BaseWithDb
{
    function index()
    {
        $db = PoolManager::getInstance()->getPool('mysql3')->getObj();
        $data = $db->get('member');
        $db->resetDbStatus();//重置为初始状态,否则回收之后会出问题
        PoolManager::getInstance()->getPool('mysql3')->recycleObj($db);
        $this->response()->write(json_encode($data));
        // TODO: Implement index() method.
    }
}