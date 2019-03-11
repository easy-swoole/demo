<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/11 0011
 * Time: 11:41
 */

namespace App\console;


use EasySwoole\Console\ModuleInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class TestConsole implements ModuleInterface
{
    /**
     * 命令执行
     * exec
     * @param Caller   $caller
     * @param Response $response
     * @author Tioncico
     * Time: 11:47
     */
    public function exec(Caller $caller, Response $response)
    {
        $args = $caller->getArgs();
        $actionName = array_shift($args);
        $caller->setArgs($args);
        switch ($actionName) {
            case 'echo':
                $this->echo($caller, $response);
                break;
            default :
                $this->help($caller, $response);
        }
        // TODO: Implement exec() method.
    }

    /**
     * 该命令的帮助
     * help
     * @param Caller   $caller
     * @param Response $response
     * @author Tioncico
     * Time: 11:48
     */
    public function help(Caller $caller, Response $response)
    {
        // TODO: Implement help() method.
        $help = <<<HELP
测试的自定义控制器

用法: 命令 [命令参数]

test echo [string]                   | 输出字符串,测试方法
HELP;
        $response->setMessage($help);
        // TODO: Implement help() method.
    }

    /**
     * 返回控制器名称
     * moduleName
     * @return string
     * @author Tioncico
     * Time: 11:48
     */
    public function moduleName(): string
    {
        return 'Test';
        // TODO: Implement moduleName() method.
    }

    /**
     * 输出方法
     * echo
     * @param $arg
     * @author Tioncico
     * Time: 11:50
     */
    private function echo(Caller $caller, Response $response)
    {
        $msg = array_shift($caller->getArgs());
        $response->setMessage($msg);
    }
}