# HTTP Controller

all controller must be a sub class of EasySwoole\Core\Http\AbstractInterface\Controller(abstract class)，and must implement index method.

## Controller Namespace

all of the controller name must be CamelCase，and below the namespace of App\HttpController.For example :
```php
<?php

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Hello extends Controller
{
    function index()
    {
        $this->response()->write('Hello easySwoole!');
    }
}
```
> access url is :/hello/index.html

## To Implement method

index method must be implement in your controller.

## Controller Default Methods And How It Work

### protected function actionNotFound($action):void
### protected function afterAction($action):void
### protected function onException(\Throwable $throwable,$actionName):void
### protected function onRequest($action):?bool    //you can intercept an request to prevent do the next action here
### protected function request():Request
### protected function response():Response
### protected function writeJson($statusCode = 200,$result = null,$msg = null)

____construct will call in dispatch ,$request and $response is all the implement of psr-7 http message 
```
public function __construct(string $actionName,Request $request,Response $response)
{
    $this->request = $request;
    $this->response = $response;
    $this->actionName = $actionName;
    if($actionName == '__construct'){
        $this->response()->withStatus(Status::CODE_BAD_REQUEST);
    }else{
        $this->__hook( $actionName);
    }
}
```
__hook function
```
protected function __hook(?string $actionName):void
{
    // onRequest if rerurn false ,means do not exec the next method
    if($this->onRequest($actionName) !== false){
       try{
            $ref = new \ReflectionClass(static::class);
            //if method is not public ,
            if($ref->hasMethod($actionName) && $ref->getMethod( $actionName)->isPublic()){
                 $this->$actionName();
            }else{
                 $this->actionNotFound($actionName);
            }
       }catch (\Throwable $throwable){
           //you can overwrite onException method to prevent throw a Throwable in a http request
           $this->onException($throwable,$actionName);
       }
       try{
            $this->afterAction($actionName);
       }catch (\Throwable $throwable){
           $this->onException($throwable,$actionName);
       }
    }   
}
```


## The Url Rule to Controller

EasySwoole use PATH_INFO mode in url parser。 for example ,your access url :
```
 /api/auth
 /api/auth/index.html
```
and the follow parser rule:
- if has 'App/HttpController/Api/Auth' controller ,then url match,action name is index,
- else if has 'App/HttpController/Api/Index' controller,then url match,action name is auth,
- else if has 'App/HttpController/Api' controller,then url match,action name is auth,
- else if has 'App/HttpController/Index' controller,then url match,action name is api,
- else if not any controller,show easySwoole default welcome page.

## The Url Max Depth 
EasySwoole parser three depth or url path for the default setting,you can change it at EasySwooleEvent.php
```
public static function frameInitialize(): void
{
	Di::getInstance()->set(SysConst::HTTP_CONTROLLER_MAX_DEPTH, MAX_NUM);
}
```
> URL Parser is Case Sensitive (ucfirst)