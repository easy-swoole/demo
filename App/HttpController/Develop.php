<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018/8/25
 * Time: 上午12:27
 */

namespace App\HttpController;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\Message\Status;

/**
 * 开发控制器
 * Class Develop
 * @package App\HttpController
 */
class Develop extends BaseWithDb
{
    /**
     * 读取一个数据库的表定义
     * @author: eValor < master@evalor.cn >
     * @throws \Exception
     */
    function bean()
    {
        $type = $this->request()->getRequestParam('type');
        $table = $this->request()->getRequestParam('table');
        $dbConnect = $this->getDbConnection();
        $database = $dbConnect->rawQuery('select database() as dbName')[0]['dbName'];
        $tables = $dbConnect->rawQuery('show tables');
        $tables = array_column($tables, "Tables_in_{$database}");
        $template = '';
        foreach ($tables as $tableName) {
            if ($table !== null && $table !== $tableName) continue;
            $template .= "\n// {$tableName}\n\n";
            $columns = $dbConnect->rawQuery("SHOW FULL COLUMNS FROM {$tableName}");
            if (!$columns) {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, '字段为空 或表不存在');
            } else {
                $max = 0;
                foreach ($columns as $item) if (strlen($item['Field']) > $max) $max = strlen($item['Field']);
                foreach ($columns as $column) {
                    $fieldName = $column['Field'];
                    $fieldComment = $column['Comment'];
                    if ($type === 'doc') {
                        $template .= "- {$fieldName}";
                        if ($fieldComment) $template .= " // {$fieldComment}";
                        $template .= "\n";
                    } else if ($type === 'join') {
                        $template .= "'{$tableName}.{$fieldName}',\n";
                    } else {
                        $fieldName = str_pad($column['Field'] . ';', $max + 1, ' ', STR_PAD_RIGHT);
                        $template .= "protected \${$fieldName} // {$fieldComment}\n";
                    }
                }
            }
        }
        $this->response()->withHeader('Content-Type', 'text/plain;charset=utf-8');
        $this->response()->write($template);
    }

    /**
     * 重载服务
     * @author: eValor < master@evalor.cn >
     */
    function reload()
    {
        $svn = `svn up /linlang3/`;
        ServerManager::getInstance()->getSwooleServer()->reload();
        $this->response()->withHeader('Content-Type', 'text/plain;charset=UTF8');
        $this->response()->write($svn . PHP_EOL . 'reload at ' . date('Y-m-d H:i:s'));
    }
}