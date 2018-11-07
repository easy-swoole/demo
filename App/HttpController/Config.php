<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/1 0001
 * Time: 11:10
 */

namespace App\HttpController;


use EasySwoole\EasySwoole\Config as EasySwooleConfig;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;

/**
 * 本类用于测试动态配置和普通配置的区别,普通配置在控制器修改之后,只针对该控制器的worker进程有效,其他进程的值无法修改,而动态配置是进程通用的配置,本进程修改,其他进程都会更新值
 * Class Config
 * @package App\HttpController
 */
class Config extends Controller
{
    function index()
    {
        $test_config_value_1 = EasySwooleConfig::getInstance()->getDynamicConf('test_config_value');
        $test_config_value_2 = EasySwooleConfig::getInstance()->getConf('test_config_value');
        $this->response()->write("动态配置值:{$test_config_value_1},普通配置值:{$test_config_value_2}");
        // TODO: Implement index() method.
    }

    function set()
    {
        EasySwooleConfig::getInstance()->delDynamicConf('test_config_value');
        EasySwooleConfig::getInstance()->setConf('test_config_value',2);

    }


}