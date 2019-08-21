<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/29 0029
 * Time: 10:45
 */

namespace App\HttpController\Api;


use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

abstract class ApiBase extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->actionNotFound('index');
    }

    protected function actionNotFound(?string $action): void
    {
        $this->writeJson(Status::CODE_NOT_FOUND);
    }

    function onRequest(?string $action): ?bool
    {
        if (!parent::onRequest($action)) {
            return false;
        };
        /*
         * 各个action的参数校验
         */
        $v = $this->getValidateRule($action);
        if ($v && !$this->validate($v)) {
            $this->writeJson(Status::CODE_BAD_REQUEST, ['errorCode' => 1, 'data' => []], $v->getError()->__toString());
            return false;
        }
        return true;
    }

    abstract protected function getValidateRule(?string $action): ?Validate;


    protected function onException(\Throwable $throwable): void
    {
        Trigger::getInstance()->throwable($throwable);
        $this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, $throwable->getMessage() . " at file {$throwable->getFile()} line {$throwable->getLine()}");
    }

    /**
     * 获取用户的get/post的一个值,可设定默认值
     * input
     * @param      $key
     * @param null $default
     * @return array|mixed|null
     * @author Tioncico
     * Time: 17:27
     */
    protected function input($key, $default = null)
    {
        $value = $this->request()->getRequestParam($key);
        return $value ?? $default;
    }

    /**
     * 获取用户的真实IP
     * @param string $headerName 代理服务器传递的标头名称
     * @return string
     */
    protected function clientRealIP($headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $this->request()->getHeader($headerName);
        $xff = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }
}