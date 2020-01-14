<?php
include "./vendor/autoload.php";

\EasySwoole\EasySwoole\Core::getInstance()->initialize();

echo md5(123456);