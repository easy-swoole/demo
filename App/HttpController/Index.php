<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-05
 * Time: 20:51
 */

namespace App\HttpController;


use App\Model\ConditionBean;
use App\Model\Member\MemberBean;
use App\Model\Member\MemberModel;
use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Spl\SplBean;

/**
 * model 1写法控制器
 * Class Index
 * @package App\HttpController
 */
class Index extends Controller
{

    /**
     * model写法1操作数据库
     * index
     * @author Tioncico
     * Time: 14:38
     */
    function index()
    {
        $data = MysqlPool::invoke(function (MysqlObject $db) {
            $memberModel = new MemberModel($db);
            //new 一个条件类,方便传入条件
            $conditionBean = new ConditionBean();
            $conditionBean->addWhere('name', '', '<>');

            return $memberModel->getAll($conditionBean->toArray([], SplBean::FILTER_NOT_NULL));
        });
        $this->response()->write(json_encode($data));
        // TODO: Implement index() method.
    }

    function add()
    {
        return MysqlPool::invoke(function (MysqlObject $db) {
            $memberModel = new MemberModel($db);
            $memberBean = new MemberBean();
            $memberBean->setMobile(123156);
            $memberBean->setName('仙士可');
            $memberBean->setPassword(md5(123456));
            $result = $memberModel->register($memberBean);
            if ($result === false) {
                $this->response()->withStatus(Status::CODE_BAD_REQUEST);
                $this->response()->write('新增错误!');
                return false;
            }
            $this->response()->write('新增成功');
        });
    }

}