<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-12-11
 * Time: 上午9:34
 */

require_once __DIR__."/../vendor/autoload.php";

$fruit = ['apple', 'orange', 'banana'];
$l = new \EasySwoole\Trace\Bean\Location();
$l->setFile(__FILE__);
$l->setLine(10);
\EasySwoole\EasySwoole\Trigger::getInstance()->error('Undefined index: key', $l);
\EasySwoole\EasySwoole\Trigger::getInstance()->throwable(new \Exception("hello easyswoole"));
var_dump($fruit);

