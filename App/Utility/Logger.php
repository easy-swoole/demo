<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Utility;

use EasySwoole\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * 打印到控制台并记录日志
     * console
     */
    public function console(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'console')
    {
        echo $msg;
    }

    /**
     * 自定义进行日志存储,比如存到数据库,存到文件,或者请求其他地方存储
     * log
     */
    public function log(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'debug')
    {
        file_put_contents(EASYSWOOLE_TEMP_DIR.'/test.log', $msg.PHP_EOL, FILE_APPEND);
    }
}
