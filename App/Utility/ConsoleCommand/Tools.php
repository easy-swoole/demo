<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/29
 * Time: 9:59 PM
 */

namespace App\Utility\ConsoleCommand;

use EasySwoole\EasySwoole\Console\CommandInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;
// 在EasySwooleEvent 添加
// use App\Utility\ConsoleCommand\Tools;
// CommandContainer::getInstance()->set('Tools',new \Tools());

/**
 * console 辅助工具
 * Class Help
 * @package EasySwoole\EasySwoole\Console\DefaultCommand
 */
class Tools implements CommandInterface
{

    /**
     * 获取某个命令的帮助信息
     * @param Caller $caller
     * @param Response $response
     * @author: eValor < master@evalor.cn >
     */
    public function exec(Caller $caller, Response $response)
    {
        $args = $caller->getArgs();
        $func = array_shift($args);
        if (!isset($func)) {
            $this->help($caller, $response);
        } else {
            switch ($func){
                case 'version':
                    if(empty($type = array_shift($args))){
                        $response->setMessage('please version type |请输入版本控制器类型');
                    }else{
                        if($type=='svn'){
                            $str = shell_exec('svn info '.EASYSWOOLE_ROOT);
                            $response->setMessage($str);
                        }else{
                            $str = shell_exec('git rev-parse HEAD');
                            $response->setMessage($str);
                        }
                    }
                    break;
                case 'showServerTime':
                    date_default_timezone_set('Etc/GMT-8');
                    $response->setMessage('date:'.date("Y-m-d H:i:s",time()));
                    break;
                case 'serverTopInfo':
                    $topInfo = shell_exec('top -n 1').PHP_EOL;
                    $response->setMessage($topInfo);
                    break;
                case 'showLog':
                    $topInfo= shell_exec('cat '.EASYSWOOLE_ROOT.'/Log/swoole.log| tail -n 10 ').PHP_EOL;
                    $response->setMessage($topInfo);
                    break;
                case 'crontabInfo':
                    $crontabInfo = shell_exec('crontab -l').PHP_EOL;
                    $response->setMessage($crontabInfo);
                    break;
                case 'cat':
                    if(empty($path = array_shift($args))){
                        $response->setMessage('please Fill out the document path |请输入版本控制器类型');
                    }else{
                        $file = EASYSWOOLE_ROOT.'/'.$path;
                        if(file_exists($file)){
                            $cat = shell_exec('cat '.$file).PHP_EOL;
                            $response->setMessage($cat);
                        }else{
                            $response->setMessage('file not exists:'.$file);
                        }
                    }
                    break;
            }
        }
    }

    public function help(Caller $caller, Response $response)
    {

        $help = <<<HELP
用法: 命令 [命令参数]
(需要开启|Need to open)  shell_exec() 
Tools version git|svn  # (svn info) (git rev-parse HEAD)
Tools showServerTime    
Tools serverTopInfo #top -n 1
Tools showLog #show swoole log
Tools crontabInfo #crontab -l
Tools cat {file path} #cat EASYSWOOLE_ROOT.'/'.{file path}
HELP;
        $response->setMessage($help);
    }
}
