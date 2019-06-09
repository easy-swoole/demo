<?php


namespace App\Device;


use EasySwoole\Component\TableManager;
use Swoole\Table;

class DeviceManager
{
    /*
     * 初始化一个table用来存储设备信息
     */
    public static function tableInit()
    {
        TableManager::getInstance()->add('device_list',[
            'actorId'=>[
                'type'=>Table::TYPE_STRING,
                'size'=>25
            ],
            'deviceId'=>[
                'type'=>Table::TYPE_STRING,
                'size'=>25
            ],
            'fd'=>[
                'type'=>Table::TYPE_INT,
                'size'=>4
            ]
        ],1024);
    }

    public static function deviceInfo(string $deviceId):?DeviceBean
    {
        $ret = self::getTable()->get($deviceId);
        if(is_array($ret)){
            return new DeviceBean($ret);
        }
        return null;
    }

    public static function deleteDevice(string $deviceId)
    {
        self::getTable()->del($deviceId);
    }

    /*
     * swoole table是字段单独更新
     */
    public static function updateDeviceInfo(string $deviceId,array $data)
    {
        self::getTable()->set($deviceId,$data);
    }

    public static function addDevice(DeviceBean $bean)
    {
        self::getTable()->set($bean->getDeviceId(),$bean->toArray());
    }

    public static function deviceInfoByFd(int $fd):?DeviceBean
    {
        $ret = self::getTable();
        foreach ($ret as $item)
        {
            if($item['fd'] == $fd){
                return new DeviceBean($item);
            }
        }
        return null;
    }

    public static function getTable():Table
    {
        return TableManager::getInstance()->get('device_list');
    }
}