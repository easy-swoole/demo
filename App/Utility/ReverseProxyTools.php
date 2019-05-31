<?php

namespace App\Utility;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Request;

/**
 * 由于反向代理环境下获取到的IP不准确
 * 该类库可以提供从反向代理获取真实客户IP以及域名的能力(需要代理配合透传)
 * Class ReverseProxyTools
 * @package App\Utility
 */
class ReverseProxyTools
{
    /**
     * 获取当前客户端的真实IP
     * @param Request $request
     * @param string $headerName
     * @return mixed|string
     */
    public static function checkCurrentClientIP(Request $request, $headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($request->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $request->getHeader($headerName);
        $xff = $request->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }

    /**
     * 获取当前的公网IP(从宝塔接口获取)
     * @param mixed $default 获取失败时返回指定的值
     * @param bool $force 是否强制从宝塔获取
     * @return bool
     */
    public static function currentPublicIPAddress($default = false, $force = false)
    {
        $cache = Cache::getInstance()->get('localIPAddress');

        if (!$force && $cache && $cache['lifeTime'] > time()) {
            return $cache['localIPAddress'];
        }

        // 从宝塔获取当前的IP
        $ipAddress = file_get_contents('https://www.bt.cn/Api/getIpAddress');
        $ipAddressRegex = '/(2(5[0-5]{1}|[0-4]\d{1})|[0-1]?\d{1,2})(\.(2(5[0-5]{1}|[0-4]\d{1})|[0-1]?\d{1,2})){3}/';

        // 返回不是一个有效的IP
        if (!$ipAddress || !preg_match($ipAddressRegex, $ipAddress)) {
            return $default;
        }

        Cache::getInstance()->set('localIPAddress', [
            'lifeTime' => time() + 60 * 60 * 24,  // 86400 = 1day
            'localIPAddress' => $ipAddress
        ]);

        return $ipAddress;
    }

    /**
     * 获取当前的域名
     * @param Request $request
     * @param bool $default 获取失败时返回指定的值
     * @param string $headerName
     * @return mixed|string
     */
    public static function checkCurrentDomain(Request $request, $default = false, $headerName = 'host')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($request->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $headerHost = $request->getHeader($headerName);
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }
}