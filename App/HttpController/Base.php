<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/18 0018
 * Time: 10:45
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\EasySwoole\Trigger;

abstract class Base extends Controller
{
    function index()
    {
        $this->actionNotFound('index');
        // TODO: Implement index() method.
    }

    protected function onRequest(?string $action): ?bool
    {
        //模拟拦截
        //当没有传code的时候则拦截
        if (empty($this->request()->getRequestParam('code'))) {
            $this->writeJson(Status::CODE_BAD_REQUEST, ['errorCode' => 1, 'data' => []], 'code不存在');
            return false;
        }
        return true;
    }

    protected function onException(\Throwable $throwable): void
    {
        //拦截错误进日志,使控制器继续运行
        Trigger::getInstance()->throwable($throwable);
        $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, $throwable->getMessage());
    }

}