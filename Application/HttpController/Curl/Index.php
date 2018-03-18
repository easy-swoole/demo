<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午5:06
 */

namespace App\HttpController\Curl;


use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Swoole\Coroutine\Client\Http;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Random;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $post = $this->request()->getParsedBody();
        $get = $this->request()->getQueryParams();
        $this->response()->write('success'.$this->request()->getQueryParam('id'));
    }

    function test()
    {
        $req = new Request('http://127.0.0.1:9501/curl/index.html?a=1&b=2');
        $req->setUserOpt([
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>[
                'post1'=>time(),
                'post2'=>Random::randStr(5)
            ]
        ]);
        $content = $req->exec()->getBody();
        var_dump($content);
        $this->response()->write('exec success');
    }



    function sleep()
    {
        $time = intval($this->request()->getRequestParam('time'));

        usleep($time*100000);

        $this->response()->write("sleep {$time}");
    }


    function concurrent()
    {
        //以下流程网络IO的时间就接近于 MAX(q1网络IO时间, q2网络IO时间)。
        $micro = microtime(true);
        $q1 = new Http('http://127.0.0.1:9501/curl/sleep/index.html?time=1');
        $c1 = $q1->exec(true);

        $q2 = new Http('http://127.0.0.1:9501/curl/sleep/index.html?time=4');
        $c2 = $q2->exec(true);

        $c1->recv();
        $c1->close();
        $c2->recv();
        $c2->close();

        var_dump($c1->body);
        var_dump($c2->body);

        $time = round(microtime(true) - $micro,3);
        $this->response()->write($time);

    }

    function concurrent2()
    {
        //以下流程网络IO的时间就接近于 MAX(q1网络IO时间, q2网络IO时间)。
        $micro = microtime(true);

        $ret = [];
        for($i=0;$i<1000;$i++){
            $ret[$i] = (new Http('http://127.0.0.1:9501/curl/index.html?id='.$i))->exec(true);
        }

        for($i=0;$i<1000;$i++){
            $ret[$i]->recv();
            $ret[$i]->close();
            $ret[$i] = $ret[$i]->body;
        }
        var_dump($ret);

        $time = round(microtime(true) - $micro,3);
        $this->response()->write($time);

    }

    function noConcurrent()
    {
        //传统阻塞
        $micro = microtime(true);
        $req = new Request('http://127.0.0.1:9501/curl/sleep/index.html?time=1');
        var_dump($req->exec()->getBody());

        $req2 = new Request('http://127.0.0.1:9501/curl/sleep/index.html?time=4');
        var_dump($req2->exec()->getBody());

        $time = round(microtime(true) - $micro,3);
        $this->response()->write($time);
    }

}