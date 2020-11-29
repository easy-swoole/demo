<?php


namespace EasySwoole\EasySwoole;


use App\Session\RedisSessionHandel;
use App\Utility\RedisClient;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Config as GlobalConfig;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Message\Request;
use EasySwoole\Http\Response;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Session\Session;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        self::redisInit();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //可以自己实现一个标准的session handler
        $handler = new RedisSessionHandel();
        //表示cookie name   还有save path
        Session::getInstance($handler);

        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST,function (Request $request,Response $response){
            $cookie = $request->getCookieParams('easy_session');
            if(empty($cookie)){
                $sid = Session::getInstance()->sessionId();
                $response->setCookie('easy_session',$sid);
            }else{
                Session::getInstance()->sessionId($cookie);
            }
            return true;
        });
    }


    /**
     * redis 连接池初始化
     * redisInit
     * @throws \EasySwoole\Pool\Exception\Exception
     * @throws \EasySwoole\RedisPool\Exception\Exception
     * @throws \EasySwoole\RedisPool\RedisPoolException
     * @author tioncico
     * Time: 10:41 上午
     */
    public static function redisInit()
    {
        //注册redis
        $config = new RedisConfig(GlobalConfig::getInstance()->getConf('REDIS'));
        $redisPoolConfig = Redis::getInstance()->register('redis', $config);
        $redisPoolConfig->setMaxObjectNum(40);
    }
}
