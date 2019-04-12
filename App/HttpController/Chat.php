<?php

namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;

use EasySwoole\Component\Pool\PoolManager;
use App\Utility\Pool\MysqlPool;

use EasySwoole\Http\Message\Status;

use App\Beans\ChatRoomBean;

/**
 * Class Chat
 * @package App\HttpController
 */
class Chat extends Controller
{
    /**
     * 创建聊天室，聊天室有有效期，30分钟、1小时、2小时、8小时、24小时
     */
    function create()
    {        
        $request = $this->request();
        
        $sever_params = $request->getServerParams();
        $method = $sever_params['request_method'];
        
        if ('GET' === $method)
        {
            ob_start();
            include dirname(__FILE__) . '/../Views/create.php';
            $content = ob_get_clean();
            $this->response()->write($content);
        }
        else if ('POST' == $method)
        {            
            $name = $request->getRequestParam('name');
            $subject = $request->getRequestParam('subject');
            
            $type = 0;
            $creator = 'admin';
            $creator_id = 1;
            $create_at = date('Y-m-d H:i:s');
            $capacity = 30;
            $duration = 1 * 60 * 60;
            
            $chatroom = new ChatRoomBean(
                            array(
                                'name' => $name,
                                'type' => $type,
                                'creator' => $creator,
                                'creator_id' => $creator_id,
                                'subject'   => $subject,
                                'capacity'  => $capacity,
                                'create_at' => $create_at,
                                'duration' => $duration
                                
                            ));
            
            $chatroom_array = $chatroom->toArray(null, ChatRoomBean::FILTER_NOT_NULL);
            
            $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
            
            $insert_id = $db->insert('chat_room', $chatroom_array);
            
            $res = array('id' => $insert_id, 'error' => $db->getLastError());
            //使用完毕需要回收
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db); 
            
            
            
            $this->writeJson(Status::CODE_OK, $res, 'new chatroot created'); 
        }

    }
    
    /**
     * 进入聊天室, 私密聊天室可能需要验证码，创建时保存在数据库里
     */
    function chat()
    {
        $server = Config::getInstance()->getConf('SYSTEM.WS_SERVER_PATH');
        $vars = ['server' => rtrim($server, '/') . '/'];
        ob_start();
        extract($vars);
        include dirname(__FILE__) . '/../Views/chat.php';
        $content = ob_get_clean();
        $this->response()->write($content);        
    }
    
    function checkmysql()
    {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        $data = $db->get('a');
        
        //使用完毕需要回收
        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
        $this->response()->write(json_encode($data));
    }
    
    /**
     * 展示Chat Room列表
     */
    function index()
    {        
        $server = Config::getInstance()->getConf('SYSTEM.WS_SERVER_PATH');
        $vars = ['server' => rtrim($server, '/') . '/'];
        ob_start();
        extract($vars);
        include dirname(__FILE__) . '/../Views/index.php';
        $content = ob_get_clean();
        $this->response()->write($content);
    }
    
    /**
     * 展示chat Room列表，以JSON返回
     */
    function nirs()
    {
        $chatRoom = array(array('id' => 34589, 'name'=> 'Room01', 'desc' => 'a new chatroom', 
                          'inviteUrl' => 'https://baidu.com'),
                          array('id' => 3459, 'name'=> 'Room02', 'desc' => 'another new chatroom', 
                          'inviteUrl' => 'https://baidu.com'));
        $this->writeJson(Status::CODE_OK, $chatRoom, 'new chatroot created');        
    }
}
