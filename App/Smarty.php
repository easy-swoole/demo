<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App;

use EasySwoole\Template\RenderInterface;
use Throwable;

class Smarty implements RenderInterface
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();
        $this->smarty->setCompileDir(EASYSWOOLE_ROOT.'/Temp/compile_s/');
        $this->smarty->setCacheDir(EASYSWOOLE_ROOT.'/Temp/cache_s/');
        $this->smarty->setTemplateDir(EASYSWOOLE_ROOT.'/Static/Smarty/');
    }

    public function render(string $template, array $data = [], array $options = []): ?string
    {
        foreach ($data as $key => $item) {
            $this->smarty->assign($key, $item);
        }
        return $this->smarty->fetch($template);
    }

    public function afterRender(?string $result, string $template, array $data = [], array $options = [])
    {
    }

    public function onException(Throwable $throwable): string
    {
        // TODO: Implement onException() method.
    }
}
