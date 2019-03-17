<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 19:08
 */

namespace App\WebSocket;

use App\Task\BroadcastTask;
use App\Utility\AppConst;
use App\Utility\Pool\RedisPool;
use App\Utility\Pool\RedisPoolObject;
use App\WebSocket\Actions\Broadcast\BroadcastAdmin;
use App\WebSocket\Actions\User\UserInRoom;
use App\WebSocket\Actions\User\UserOutRoom;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\EasySwoole\Config;

class WebSocketEvents
{
    /**
     * 链接打开时 将用户的FD存入Redis
     * @param \swoole_server $server
     * @param \swoole_http_request     $req
     * @throws \Exception
     */
    static function onOpen(\swoole_server $server, \swoole_http_request $req)
    {
        $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
        $redis = $redisPool->getObj();
        $username = $req->get['username'] ?? '吃瓜乘客' . str_pad($req->fd, 4, '0', STR_PAD_LEFT);
        if ($redis instanceof RedisPoolObject) {
            $info = self::mockUser($req->fd, $username);
            $redis->hSet(AppConst::REDIS_ONLINE_KEY, $req->fd, $info);
            $redis->incr(AppConst::SYSTEM_CON_COUNT_KEY);
            $count = $redis->get(AppConst::SYSTEM_CON_COUNT_KEY);

            // 全频道通知新用户上线
            $message = new UserInRoom;
            $message->setInfo($info);
            TaskManager::async(new BroadcastTask(['payload' => $message->__toString(), 'fromFd' => $req->fd]));

            if (empty($req->get['is_reconnection']) || $req->get['is_reconnection'] == '0') {
                // 发送最后99条数据
                $lastMessage = $redis->lRange(AppConst::REDIS_LAST_MESSAGE_KEY, 0, Config::getInstance()->getConf('SYSTEM.LAST_MESSAGE_MAX'));
                for ($i = count($lastMessage) - 1; $i >= 0; $i--) {
                    $server->push($req->fd, $lastMessage[$i]);
                }

                // 对该用户单独发送欢迎消息
                $runDays = intval((time() - ($redis->get(AppConst::SYSTEM_RUNTIME_KEY))) / 86400);
                $message = new BroadcastAdmin;
                $message->setContent("{$username}，欢迎乘坐EASYSWOOLE号特快列车，列车已稳定运行{$runDays}天，共计服务{$count}人次，请系好安全带，文明乘车");
                $server->push($req->fd, $message->__toString());
            }

            $redisPool->recycleObj($redis);
            echo "websocket user {$req->fd} was connected\n";
        } else {
            throw new \Exception('redis pool is empty');
        }
    }

    /**
     * 链接关闭时 将用户的FD从Redis删除
     * @param \swoole_server $server
     * @param int                      $fd
     * @param int                      $reactorId
     * @throws \Exception
     */
    static function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        $info = $server->connection_info($fd);
        if ($info['websocket_status'] !== 0) {
            $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
            $redis = $redisPool->getObj();
            if ($redis instanceof RedisPoolObject) {
                $redis->hDel(AppConst::REDIS_ONLINE_KEY, $fd);

                // 全频道通知用户已离线
                $message = new UserOutRoom;
                $message->setUserFd($fd);
                TaskManager::async(new BroadcastTask(['payload' => $message->__toString(), 'fromFd' => $fd]));

                $redisPool->recycleObj($redis);
                echo "websocket user {$fd} was close\n";
            } else {
                throw new \Exception('redis pool is empty');
            }
        }
    }

    /**
     * 清空在线用户列表
     * @throws \Exception
     */
    static function cleanOnlineUser()
    {
        $redisPool = PoolManager::getInstance()->getPool(RedisPool::class);
        $redis = $redisPool->getObj();
        if ($redis instanceof RedisPoolObject) {
            $redis->set(AppConst::SYSTEM_RUNTIME_KEY, time());
            $redis->del(AppConst::SYSTEM_CON_COUNT_KEY);
            if ($redis->exists(AppConst::REDIS_ONLINE_KEY)) {
                $clear = $redis->del(AppConst::REDIS_ONLINE_KEY);
                $status = $clear ? 'success' : 'failed';
                echo "Redis online user clean {$status}\n";
            }
            $redisPool->recycleObj($redis);
        } else {
            throw new \Exception('redis pool is empty');
        }
    }

    /**
     * 生产一个游客用户
     * @param int     $userFd
     * @param  string $userName
     * @return array
     */
    static private function mockUser($userFd, $userName)
    {
        mt_srand();
        $introduce = ['请叫我秋名山车神', '这不是去学校的车', '最长的路是你的套路', '车速超快我有点怕', '最美的风景是在路上', '身娇腰柔易推倒', '时光静好与君语', '细水流年与君同', '繁华落尽与君老', '吃瓜什么的最棒了'];
        return ['username' => $userName, 'userFd' => $userFd, 'intro' => $introduce[rand(0, 9)]];
    }
}
