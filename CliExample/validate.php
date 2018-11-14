<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午3:24
 */

// 加载库文件
require_once __DIR__."/../vendor/autoload.php";

$data = [
    'name' => 'blank',
    'age' => 31
];

$validate = new \EasySwoole\Validate\Validate();
// 为字段加入验证规则
$validate->addColumn('name')->required('name', '姓名不能为空');
$validate->addColumn('age')->required('age', '年龄不能为空')->max(30, '年龄不能超过30');

// 验证是否通过
if ($validate->validate($data)) {
    echo '验证通过';
} else {
    echo $validate->getError()->getErrorRuleMsg();
}