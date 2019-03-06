<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/6 0006
 * Time: 14:46
 */

namespace App\HttpController;

use App\Model\ConditionBean;
use App\Model\Member\Member4Model;
use App\Model\Member\MemberModel;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Spl\SplBean;

/**
 * 直接实例化model的示例
 * Class Index2
 * @package App\HttpController
 */
class Mysql4 extends BaseWithDb
{
    function index()
    {
        //由于Member4Model构造函数已经获取了一条数据库连接
        //在析构函数中又释放了,所以可以直接new model使用
        $memberModel = new Member4Model();
        //new 一个条件类,方便传入条件
        $conditionBean = new ConditionBean();
        $conditionBean->addWhere('name', '', '<>');
        $data = $memberModel->getAll($conditionBean->toArray([], SplBean::FILTER_NOT_NULL));
        $this->response()->write(json_encode($data));
        // TODO: Implement index() method.
    }
}