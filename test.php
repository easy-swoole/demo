<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/5
 * Time: 下午10:06
 */
require 'vendor/autoload.php';
\EasySwoole\Core\Core::getInstance()->initialize();

$model = new \App\Model\User\User();
