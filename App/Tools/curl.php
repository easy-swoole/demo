<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午3:00
 */

// 加载库文件
require_once __DIR__."/../../vendor/autoload.php";


function getLyric() {
    $url = 'https://c.y.qq.com/lyric/fcgi-bin/fcg_query_lyric.fcg';
    $params = [
        'nobase64' => 1,
        'musicid' => 109332150,
        'inCharset' => 'utf8',
        'outCharset' => 'utf-8'
    ];
    // 绑定url链接
    $request = new \EasySwoole\Curl\Request($url);
    foreach ($params as $key => $value) {
        $field = new \EasySwoole\Curl\Field($key, $value);
        // 设置参数
        $request->addGet($field);
    }
    // 设置referer
    $request->setUserOpt([CURLOPT_REFERER => 'https://y.qq.com/n/yqq/song/001xiJdl0t4NgO.html']);
    $content = $request->exec()->getBody();
    $string = new \EasySwoole\Spl\SplString($content);
    $content = $string->regex('/\{.*\}/');
    $json = json_decode($content, true);
    $lyric = $json['lyric'];
    echo html_entity_decode($lyric);
}

getLyric();