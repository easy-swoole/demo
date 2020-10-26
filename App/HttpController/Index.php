<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Base
{
    public function index()
    {
        $hostName = $this->cfgValue('WEBSOCKET_HOST', 'ws://127.0.0.1:9501');
        $this->render('index', [
            'server' => $hostName
        ]);
    }
}
