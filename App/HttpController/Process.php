<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;

class Process extends Controller
{
    public function write()
    {
        /** @var \Swoole\Process $process */
        $process = Di::getInstance()->get('processOne');

        // 获取参数
        $text = $this->request()->getQueryParam('text') ?? 'text';

        // 向进程写入数据
        $process->write($text);

        $this->response()->write($text);
    }
}
