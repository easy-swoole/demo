<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Task;

use EasySwoole\Task\AbstractInterface\TaskInterface;
use Throwable;

class TestTask implements TaskInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run(int $taskId, int $workerIndex)
    {
        var_dump('模板任务运行');
        var_dump($this->data);
        //只有同步调用才能返回数据
        return '返回值:' . $this->data['name'];
    }

    public function onException(Throwable $throwable, int $taskId, int $workerIndex)
    {
        var_dump($throwable->getMessage());
    }
}
