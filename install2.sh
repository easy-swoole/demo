#!/bin/bash
LANG=en_US.UTF-8

echo "
+--------------------------------------------------
| EasySwoole 2.x installer
+--------------------------------------------------
| https://www.easyswoole.com
+--------------------------------------------------
"

command -v composer >/dev/null 2>&1 || { echo -e >&2 "\033[31mComposer does not exist! Initialization cannot continue...\033[0m"; exit 1; }
command -v curl >/dev/null 2>&1 || { echo -e >&2 "\033[31mCurl does not exist! Initialization cannot continue...\033[0m"; exit 1; }

COMPOSER_NAME="--name easyswoole/application"
COMPOSER_DESCRIPTION="--description application"
COMPOSER_TYPE="--type project"
COMPOSER_RE1="--require "easyswoole/easyswoole=2.x-dev""
COMPOSER_RE2="--require-dev "easyswoole/swoole-ide-helper=dev-master""

echo -e "\033[32mClean up the project environment...\033[0m"

rm -rf vendor composer.json composer.lock Log Temp Config.php easyswoole easyswoole.install EasySwooleEvent.php

echo -e "\033[32mCreate the composer definition file in the current directory...\033[0m"
composer init ${COMPOSER_NAME} ${COMPOSER_DESCRIPTION} ${COMPOSER_TYPE}  ${COMPOSER_RE1} ${COMPOSER_RE2} --quiet
echo -e "\033[32mRun Composer install...\033[0m"
composer install

echo -e "\033[32mRun easyswoole install...\033[0m"

php vendor/bin/easyswoole install
php easyswoole start
