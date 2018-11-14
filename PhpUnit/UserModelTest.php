<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-13
 * Time: 上午10:04
 */

namespace PhpUnit;


use App\Model\User\UserBean;
use App\Model\User\UserModel;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{

    function testUpdateUserName() {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        if ($db) {
            $userModel = new UserModel($db);
            $userBean = new UserBean(['id' => 3]);
            // 获取更新前user信息
            $originUser = $userModel->getOne($userBean);
            if (!empty($originUser)) {
                // 获取更新前user名字
                $originName = $originUser->getName();
                $name = '王丽';
                $userModel->update($userBean, ['name' => $name]);
                $currentUser = $userModel->getOne($userBean);
                if (!empty($currentUser)) {

                    // 名字是否被更新
                    $this->assertEquals($name, $currentUser->getName());
                    $user = $currentUser;
                    $user->setName($originName);
                    $this->assertEquals(json_encode($user), json_encode($originUser));
                } else {
                    PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
                    throw new \PHPUnit\Framework\Exception('can not find this people', 400);
                }
            } else {
                PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
                throw new \PHPUnit\Framework\Exception('can not find this people', 400);
            }
        } else {
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
            throw new \PHPUnit\Framework\Exception('db pool is empty', 400);
        }
        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
    }
}