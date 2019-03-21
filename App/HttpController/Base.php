<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-20
 * Time: 16:59
 */

namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;

/**
 * Class Base
 * @package App\HttpController
 */
class Base extends Controller
{
    function index()
    {
        $this->actionNotFound($this->getActionName());
    }

    /**
     * 获取当前站点域名
     * @return string
     */
    function host()
    {
        return rtrim(Config::getInstance()->getConf('HOST'), '/');
    }

    /**
     * 渲染一个模板页面
     * @param $tplName
     * @param array $tplVars
     * @throws \Exception
     */
    function renderTemplate($tplName, array $tplVars = [])
    {
        $staticPath = EASYSWOOLE_ROOT . '/Static/';
        $templateFile = $staticPath . $tplName;
        if (is_file($templateFile)) {
            $content = file_get_contents($templateFile);
            foreach ($tplVars as $tplVarName => $tplVarValue) {
                $content = str_replace("{{{$tplVarName}}}", $tplVarValue, $content);
            }
            $this->response()->withHeader('content-type', 'text/html;charset=utf8');
            $this->response()->write($content);
        } else {
            throw new \Exception('template ' . $tplName . ' does not exist');
        }
    }
}