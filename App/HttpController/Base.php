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

abstract class Base extends Controller
{
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
        $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, $throwable->getMessage());
    }

}