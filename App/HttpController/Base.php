<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\EasySwoole\Trigger;
use Throwable;

abstract class Base extends Controller
{
    public function index()
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

    protected function onException(Throwable $throwable): void
    {
        //拦截错误进日志,使控制器继续运行
        Trigger::getInstance()->throwable($throwable);
        $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, $throwable->getMessage());
    }
}
