<?php

namespace App\Utility;

use EasySwoole\Template\RenderInterface;
use League\Plates\Engine;

/**
 * 模板渲染器
 * Class PlatesRender
 * @package App\Utility
 */
class PlatesRender implements RenderInterface
{
    private $views;
    private $engine;

    public function __construct($views)
    {
        $this->views = $views;
        $this->engine = new Engine($this->views);
    }

    /**
     * 渲染模板
     * @param string $template
     * @param array $data
     * @param array $options
     * @return string|null
     */
    public function render(string $template, array $data = [], array $options = []): ?string
    {
        // 支持模板引擎以闭包形式设置(多进程渲染时请注意进程隔离问题)
        if (isset($options['call']) && is_callable($options['call'])) {
            $options['call']($this->engine);
        }

        // 渲染并返回内容
        return $this->engine->render($template, $data);
    }

    /**
     * 渲染完成
     * @param string|null $result
     * @param string $template
     * @param array $data
     * @param array $options
     */
    public function afterRender(?string $result, string $template, array $data = [], array $options = [])
    {
        // 重新创建实例
        $this->engine = new Engine($this->views);
    }

    /**
     * 异常时可以输出错误模板
     * @param \Throwable $throwable
     * @return string
     */
    public function onException(\Throwable $throwable): string
    {
        return 'Error: ' . $throwable->getMessage();
    }
}