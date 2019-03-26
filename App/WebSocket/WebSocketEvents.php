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
        $username = $req->get['username'] ?? 'Niror_' . str_pad($req->fd, 5, '0', STR_PAD_LEFT);
        if ($redis instanceof RedisPoolObject) {
            $info = self::mockUser($req->fd, $username);

            if ($redis->exists(AppConst::REDIS_ONLINE_KEY) === FALSE) 
            {    
                $redis->hSet(AppConst::REDIS_ONLINE_KEY, $req->fd, $info);
                //当日有效
                $redis->expireAt(strtotime(date('Y-m-d 23:59:59', time())));
            }else
            {
                $redis->hSet(AppConst::REDIS_ONLINE_KEY, $req->fd, $info);                
            }
            
            if ($redis->exists(AppConst::REDIS_USERNAME_TO_USERINFO) === FALSE)
            {
                $redis->hSet(AppConst::REDIS_USERNAME_TO_USERINFO, $username, $info);
                //当日有效
                $redis->expireAt(strtotime(date('Y-m-d 23:59:59', time())));
            }
            else
            {
                $redis->hSet(AppConst::REDIS_USERNAME_TO_USERINFO, $username, $info);
            }
            
            if($redis->exists(AppConst::SYSTEM_CON_COUNT_KEY) === FALSE)
            {
                $redis->incr(AppConst::SYSTEM_CON_COUNT_KEY);
                //当日有效
                $redis->expireAt(strtotime(date('Y-m-d 23:59:59', time())));
            }else
            {
                $redis->incr(AppConst::SYSTEM_CON_COUNT_KEY);
            }

            if ($redis->exists(AppConst::REDIS_MESSAGE_RANK_KEY) === FALSE)
            {
                $redis->zAdd(AppConst::REDIS_MESSAGE_RANK_KEY, 0, $info['username']);
                
                $redis->expire(AppConst::REDIS_MESSAGE_RANK_KEY, 3600);
            }
            
            // 对该用户单独发送欢迎消息
            $message = new BroadcastAdmin;
            $message->setContent("{$username}，Welcome! Happy chat in Nirvana!");
            $server->push($req->fd, $message->__toString());
            
            if (empty($req->get['is_reconnection']) || $req->get['is_reconnection'] == '0') {
                // 发送最后99条数据
                $lastMessage = $redis->lRange(AppConst::REDIS_LAST_MESSAGE_KEY, 0, Config::getInstance()->getConf('SYSTEM.LAST_MESSAGE_MAX'));
                for ($i = count($lastMessage) - 1; $i >= 0; $i--) {
                    $server->push($req->fd, $lastMessage[$i]);
                }
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
                
                $userinfo = $redis->hGet(AppConst::REDIS_ONLINE_KEY, $fd);
                
                $redis->hDel(AppConst::REDIS_ONLINE_KEY, $fd);
                
                if ($userinfo)
                {
                    $username = $userinfo['username'];
                    
                    $redis->zRem(AppConst::REDIS_MESSAGE_RANK_KEY, $username);
                    
                    $redis->hDel(AppConst::REDIS_USERNAME_TO_USERINFO, $username);
                    
                }
                
                $redisPool->recycleObj($redis);
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
            $redis->del(AppConst::SYSTEM_CON_COUNT_KEY);
            if ($redis->exists(AppConst::REDIS_ONLINE_KEY)) {
                $redis->del(AppConst::REDIS_ONLINE_KEY);
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
        return ['username' => $userName, 'userFd' => $userFd, 'msgCnt' => 0];
    }
}
