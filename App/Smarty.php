<?php


namespace App;


use EasySwoole\Template\RenderInterface;

class Smarty implements RenderInterface
{

    protected $smarty;
    function __construct()
    {
        $this->smarty = new \Smarty();
        $this->smarty->setCompileDir(EASYSWOOLE_ROOT.'/Temp/compile_s/');
        $this->smarty->setCacheDir(EASYSWOOLE_ROOT.'/Temp/cache_s/');
        $this->smarty->setTemplateDir(EASYSWOOLE_ROOT.'/Static/Smarty/');
    }

    public function render(string $template, array $data = [], array $options = []): ?string
    {
       foreach ($data as $key => $item){
           $this->smarty->assign($key,$item);
       }
       return $this->smarty->fetch($template);
    }

    public function afterRender(?string $result, string $template, array $data = [], array $options = [])
    {

    }

    public function onException(\Throwable $throwable): string
    {
        // TODO: Implement onException() method.
    }
}