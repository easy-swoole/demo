<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-12
 * Time: 下午4:44
 */

// 加载库文件
require_once __DIR__."/../vendor/autoload.php";

//本ArrayToTextTable改编自网络，兼容适配了utf8和不再依赖第三方包

$data = [
    [
        '姓名' => 'James',
        '年龄' => '20',
        'sex'=>'男'
    ],
    [
        '姓名' => '这是测试姓名啊',
        '年龄' => 50,
        'email' => '291323003@qq.com',
    ],
];

$renderer = new \EasySwoole\Utility\ArrayToTextTable($data);
$renderer->setIndentation("\t");
$renderer->setDisplayHeader(true);
$renderer->setKeysAlignment(\EasySwoole\Utility\ArrayToTextTable::AlignLeft);
$renderer->setValuesAlignment(\EasySwoole\Utility\ArrayToTextTable::AlignLeft);
$renderer->setFormatter(function (&$value,$key){
    if($key == 'sex'){
        if(empty($value)){
            $value = '未知性别';
        }
    }else if($key == 'email'){
        if(empty($value)){
            $value = '未知邮箱';
        }
    }
});

$table =  $renderer->getTable();

echo $renderer;

