#!/bin/bash
echo "start download easyswoole…"
curl -sS -o master.tar.gz https://codeload.github.com/easy-swoole/easyswoole/tar.gz/master
echo "Unzip …"
tar -xzvf master.tar.gz
mv ./easyswoole-master/src/* ./
rm -rf ./easyswoole-master master.tar.gz
echo "install success"
exit 0;