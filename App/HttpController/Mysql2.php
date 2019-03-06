<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/6 0006
 * Time: 14:46
 */

namespace App\HttpController;

use App\Model\ConditionBean;
use App\Model\Member\Member2Model;
use EasySwoole\Spl\SplBean;

/**
 * 控制器获取数据库连接的第二种写法
 * 以及操作数据库2
 * Class Index2
 * @package App\HttpController
 */
class Mysql2 extends BaseWithDb
{
    function index()
    {
        //此时已经是操作数据库2了

        $memberModel = new Member2Model($this->getDbConnection());
        //new 一个条件类,方便传入条件
        $conditionBean = new ConditionBean();
        $conditionBean->addWhere('name', '', '<>');
        $data = $memberModel->getAll($conditionBean->toArray([], SplBean::FILTER_NOT_NULL));
        $this->response()->write(json_encode($data));
        // TODO: Implement index() method.
    }




}