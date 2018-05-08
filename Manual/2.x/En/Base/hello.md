# Hello World 
## create your application directory
for example,my app directory is App,so I do :
```
cd my_project_dir
mkdir App
```
## register composer psr-4 namespace
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "App/"
        }
    },
    "require": {
        "easyswoole/easyswoole": "2.x-dev"
    }
}
```
> don't forget to exec 'composer dump-autoload' after add the namespace

## create Index controller
- create controller directory 
```
cd App
mkdir HttpController
```
- create Index file
vim Index.php,and write :
```php
<?php
namespace App\HttpController;
use EasySwoole\Core\Http\AbstractInterface\Controller;
class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
}
```

## start your server

- cd to your project root directory
- exec command : php easyswoole start

if there is no any error,you can access it by url :http://127.0.0.1:9501 and you will
 see 'hello world'