<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午5:06
 */

namespace App\HttpController\Curl;


use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Random;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $post = $this->request()->getParsedBody();
        $get = $this->request()->getQueryParams();
        var_dump($post,$get);
        $this->response()->write('success');
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
}