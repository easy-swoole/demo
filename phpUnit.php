<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-14
 * Time: 上午10:37
 */

require_once "./vendor/autoload.php";

go(function() {
    \EasySwoole\EasySwoole\Core::getInstance()->initialize();
    require_once "./vendor/bin/phpunit";
});
