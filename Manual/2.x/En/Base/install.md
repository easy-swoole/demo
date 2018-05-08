# Install


exec command below your project directory :
- composer require easyswoole/easyswoole=2.x-dev
- php vendor/bin/easyswoole install

> if not any error and you will see 'install complete'

## composer error when exec
```
 php vendor/bin/easyswoole install
```
 
you may see :
```
dir=$(d=${0%[/\\]*}; cd "$d" > /dev/null; cd '../easyswoole/easyswoole/bin' && pwd)

# See if we are running in Cygwin by checking for cygpath program
if command -v 'cygpath' >/dev/null 2>&1; then
	# Cygwin paths start with /cygdrive/ which will break windows PHP,
	# so we need to translate the dir path to windows format. However
	# we could be using cygwin PHP which does not require this, so we
	# test if the path to PHP starts with /cygdrive/ rather than /usr/bin
	if [[ $(which php) == /cygdrive/* ]]; then
		dir=$(cygpath -m "$dir");
	fi
fi

dir=$(echo $dir | sed 's/ /\ /g')
"${dir}/easyswoole" "$@"
```
just try:
```
php vendor/easyswoole/easyswoole/bin/easyswoole install
```

## project directory after install success

```
project                   top directory
├─Log                     Log directory
├─Temp                    temo directory
├─vendor                  composer vendor directory
├─composer.json           
├─composer.lock           
├─Config.php              easySwoole config file
├─EasySwooleEvent.php     easySwoole global event file
├─easyswoole              easySwoole bin file
├─easyswoole.install      
```
