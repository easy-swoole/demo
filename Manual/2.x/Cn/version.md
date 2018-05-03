# 版本升级细微差异


## EasySwoole 2.0.1


1、EasySwooleEvent.php中的全部方法变更为静态方法。老代码中新增static关键字即可。

2、EasySwoole的socket控制器ParserInterface全部变更为静态方法，老代码请新增static关键字在协议解析器的各个方法，并修改注册的事件回调函数的第二个参数为解析器的名称:yourparser::class

## EasySwoole 2.1.1

相对于2.0.1而言，默认主配置项变动为
```
    'SERVER_NAME'=>"EasySwoole",//新增
    'MAIN_SERVER'=>[
        'HOST'=>'0.0.0.0',
        'PORT'=>9501,
        'SERVER_TYPE'=>\EasySwoole\Core\Swoole\ServerManager::TYPE_WEB_SERVER,
        'SOCK_TYPE'=>SWOOLE_TCP,//该配置项当为SERVER_TYPE值为TYPE_SERVER时有效
        'RUN_MODEL'=>SWOOLE_PROCESS,
        'SETTING'=>[
            'task_worker_num' => 8, //异步任务进程
            'task_max_request'=>10,
            'max_request'=>5000,//强烈建议设置此配置项
            'worker_num'=>8
        ],
    ],
    'DEBUG'=>true,
    'TEMP_DIR'=>EASYSWOOLE_ROOT.'/Temp',
    'LOG_DIR'=>EASYSWOOLE_ROOT.'/Log',
    'EASY_CACHE'=>[
        'PROCESS_NUM'=>1,//若不希望开启，则设置为0
        'PERSISTENT_TIME'=>0//如果需要定时数据落地，请设置对应的时间周期，单位为秒
    ],
    'CLUSTER'=>[//变动字段
        'enable'=>false,
        'token'=>null,
        'broadcastAddress'=>['255.255.255.255:9556'],
         'listenAddress'=>'0.0.0.0',
         'listenPort'=>'9556',
        'broadcastTTL'=>5,
        'nodeTimeout'=>10,
        'nodeName'=>'easySwoole',
        'nodeId'=>null
    ]
```

