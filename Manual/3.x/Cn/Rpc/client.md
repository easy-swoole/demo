##客户端

###请求格式:
客户端请求Rpc服务端json格式字符串为:
```json
    {
        "service":"serviceOne",
        "action":"funcOne",
        "args":[]
    }
```

###请求协议: 
请求协议为Tcp协议,发送数据需要转换成 "无符号、网络字节序、4字节" N 格式发送  

###请求响应步骤
 - 客户端tcp连接,发送 N 数据  
 - 服务端接收数据,解包,根据发送数据调度控制器,响应数据
 - 默认响应完数据则断开,可在控制器response配置status改为不断开
 - 客户端接收 N 数据,进行解包处理


###客户端示例:

####原生php示例:
```php
<?php
$arr = [
    'service'=>'a',
    'action'=>'test',
    'args'=>[
        'a'=>1
    ]
];

$fp = stream_socket_client('tcp://127.0.0.1:9501');

echo $sendStr = json_encode($arr);

$data = pack('N', strlen($sendStr)).$sendStr;

fwrite($fp,$data);

$data = fread($fp,65533);
//做长度头部校验
$len = unpack('N',$data);
$data = substr($data,'4');
if(strlen($data) != $len[1]){
    echo 'data error';
}else{
    $json = json_decode($data,true);
    //这就是服务端返回的结果，
    var_dump($json);
}
fclose($fp);
``` 

####原生node.js示例:
```node.js
    var net = require('net');
    var pack = require('php-pack').pack;
    var unpack = require('php-pack').unpack;
    var json = {
        service:'a',
        action:'test',
        args:[]
    };
    
    var send = JSON.stringify(json);
    
    send = Buffer.concat([pack("N",send.length), Buffer.from(send)]);
    
    var client = new net.Socket();
    client.connect(9501, '127.0.0.1', function() {
        console.log('Connected');
        client.write(send);
    
    });
    
    client.on('data', function(data) {
        console.log('Received: ' + data);
        var ret = JSON.parse(data.toString().substr(4));
        console.log('status: ' +  ret.status);
        client.destroy()
    });
    
    client.on('close', function() {
        console.log('Connection closed');
    });
    client.on('error',function (error) {
        console.log(error);
    });
```

####easyswoole框架示例:

EasySwooleEvent.php:
```php
    public static function mainServerCreate(EventRegister $register)
    {
        $rpc = RpcServer::getInstance(new \EasySwoole\Rpc\Config(),Trigger::getInstance());
        
        //注册已知节点
        $node = new ServiceNode();
        $node->setServiceName('serviceOne');
        $node->setServiceId($conf->getServiceId());
        $node->setIp('192.168.159.1');
        $node->setPort('9601');
        $node->setLastHeartBeat(time());
        RpcServer::getInstance()->refreshServiceNode($node);
        $node->setServiceName('serviceTwo');
        RpcServer::getInstance()->refreshServiceNode($node);
    }
```
控制器代码:

```php
<?php
        $msg = null;
        $t = microtime(true);
        $client = RpcServer::getInstance()->client();
        $client->addCall('serviceOne','funcOne','额外参数1','额外参数2','额外参数3')
            ->success(function (Response $response)use(&$msg){
                $msg = $response->getMessage();
            })
            ->fail(function (Response $response)use(&$msg){
                $msg = $response->__toString();
                Logger::getInstance()->console($response->__toString());
            });
//        $client->addCall('serviceOne','task')
//            ->success(function (Response $response){
//                Logger::getInstance()->console($response->__toString());
//            })
//            ->fail(function (Response $response){
//                Logger::getInstance()->console($response->__toString());
//            });
        $client->exec(0.5);//超时时间
        
        $t = round(microtime(true) - $t,3);
        $this->response()->write("rpc take {$t} s and mgs is {$msg}");
```
