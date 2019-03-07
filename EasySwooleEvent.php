<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;

use App\TcpController\Parser;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Swoole\Server;

class EasySwooleEvent implements Event
{
    /**
     * 框架初始化事件
     * 在Swoole没有启动之前 会先执行这里的代码
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');//设置时区
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $server = ServerManager::getInstance()->getSwooleServer();

        ################# tcp 服务器1 没有处理粘包 #####################
        $subPort1 = $server->addlistener('0.0.0.0', 9502, SWOOLE_TCP);
        $subPort1->set(
            [
                'open_length_check' => false,//不验证数据包
            ]
        );
        $subPort1->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务1  fd:{$fd} 已连接\n";
            $str = '恭喜你连接成功服务器1';
            $server->send($fd, $str);
        });
        $subPort1->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务1  fd:{$fd} 已关闭\n";
        });
        $subPort1->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) {
            echo "tcp服务1  fd:{$fd} 发送消息:{$data}\n";
        });


        ################# tcp 服务器2 处理粘包 #####################
        $subPort2 = $server->addlistener('0.0.0.0', 9503, SWOOLE_TCP);
        $subPort2->set(
            [
                'open_length_check'     => true,
                'package_max_length'    => 81920,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
            ]
        );
        $subPort2->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务2  fd:{$fd} 已连接\n";
            $str = '恭喜你连接成功服务器2';
            $server->send($fd, pack('N', strlen($str)) . $str);
        });
        $subPort2->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务2  fd:{$fd} 已关闭\n";
        });
        $subPort2->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) {
            echo "tcp服务2  fd:{$fd} 发送原始消息:{$data}\n";
            echo "tcp服务2  fd:{$fd} 发送消息:" . substr($data, '4') . "\n";
        });


        ############# tcp 服务器3 tcp控制器实现+处理粘包############

        $subPort3 = $server->addListener(Config::getInstance()->getConf('MAIN_SERVER.LISTEN_ADDRESS'), 9504, SWOOLE_TCP);

        $socketConfig = new \EasySwoole\Socket\Config();
        $socketConfig->setType($socketConfig::TCP);
        $socketConfig->setParser(new \App\TcpController\Parser());
        //设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function ($server, $throwable, $raw, $client, $response) {
            echo  "tcp服务3  fd:{$client->getFd()} 发送数据异常 \n";
            $server->close($client->getFd());
        });
        $dispatch = new \EasySwoole\Socket\Dispatcher($socketConfig);

        $subPort3->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
            echo "tcp服务3  fd:{$fd} 发送消息:{$data}\n";
            $dispatch->dispatch($server, $data, $fd, $reactor_id);
        });
        $subPort3->set(
            [
                'open_length_check'     => true,
                'package_max_length'    => 81920,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
            ]
        );
        $subPort3->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务3  fd:{$fd} 已连接\n";
        });
        $subPort3->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务3  fd:{$fd} 已关闭\n";
        });

    }


    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data): void
    {
        echo "TCP onReceive.\n";
    }


}
