<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/7/27
 * Time: 下午10:32
 */

namespace App\Rpc;


use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\RequestPackage;
use EasySwoole\Rpc\Response;

abstract class AbstractService
{
    private $package;
    private $response;
    private $server;
    private $config;
    private $fd;

    final function __construct(RequestPackage $package, Response $response, Config $config, \swoole_server $server, int $fd)
    {
        $this->package = $package;
        $this->response = $response;
        $this->config = $config;
        $this->server = $server;
        $this->fd = $fd;
        $this->__hook();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return RequestPackage
     */
    public function getPackage(): RequestPackage
    {
        return $this->package;
    }

    /**
     * @return \swoole_server
     */
    public function getServer(): \swoole_server
    {
        return $this->server;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    protected function onException(\Throwable $throwable)
    {
        $this->response->setStatus(Response::STATUS_SERVER_ERROR);
        $this->response->setMessage("{$throwable->getMessage()} at file {$throwable->getFile()} line {$throwable->getLine()}");
    }

    protected function actionNotFound(?string $action)
    {
        $this->getResponse()->setMessage("Service action : {$action} not found");
        $this->response->setStatus(Response::STATUS_SERVER_ERROR);
        $this->response->setMessage("Service action : {$action} not found");
    }

    protected function afterAction(?string $actionName)
    {

    }

    private function __hook()
    {
        $actionName = $this->package->getAction();
        try {
            if (method_exists($this, $actionName)) {
                //先设置默认OK
                $this->getResponse()->setStatus(Response::STATUS_OK);
                $this->$actionName();
            } else {
                $this->actionNotFound($actionName);
            }
        } catch (\Throwable $throwable) {
            //行为中的异常才触发
            $this->onException($throwable);
        } finally {
            try {
                $this->afterAction($actionName);
            } catch (\Throwable $throwable) {
                $this->onException($throwable);
            }
        }
    }
}