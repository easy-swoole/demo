<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Template\Render;

class Index extends Controller
{
    public function index()
    {
        $this->response()->write(Render::getInstance()->render('index.tpl', [
            'user'=>'easyswoole',
            'time'=>time()
        ]));
    }

    public function reload()
    {
        Render::getInstance()->restartWorker();
        $this->response()->write('restart success');
    }
}
