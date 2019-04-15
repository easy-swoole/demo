<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/15 0015
 * Time: 14:04
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{

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
}