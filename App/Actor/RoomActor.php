<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 22:00
 */

namespace App\Actor;


use App\Model\Bean\PokerCard;
use App\Model\Bean\PokerCardsContainer;
use EasySwoole\Actor\AbstractActor;
use EasySwoole\Actor\ActorConfig;
use EasySwoole\Actor\Command;

class RoomActor extends AbstractActor
{
    private $banker;
    private $currentPlayerList = [];
    private $roomPlayerList = [];
    static function configure(ActorConfig $actorConfig)
    {
        // TODO: Implement configure() method.
        $actorConfig->setActorName('room');
    }

    function onStart($arg)
    {
        // TODO: Implement onStart() method.
        /*
         * 机器人坐庄,每35s一轮游戏，15s准备，15s下注 ,5s开奖冻结期
         */
        $this->tick(35*1000,function (){
            //生成庄家牌
            $this->banker = PokerCard::getPokerGroup(5);
            //通知房间内全部成员，当前房间状态进入准备状态,并清除当轮玩家
            $this->currentPlayerList = [];
            $command = new Command();
            $command->setCommand(PlayerActor::STATUS_WAITING_READY);
            $pushData = [];
            foreach ($this->roomPlayerList as $playerActorId){
                /** @var $player PlayerActor */
               $pushData[$playerActorId] = $command;
            }
            PlayerActor::invoke()->fastPushMulti($pushData);
            //等待15s准备时间，为本轮准备的用户发牌
            \co::sleep(15);
            $pushData = [];
            foreach ($this->currentPlayerList as $playerActorId){
                $command = new Command();
                $command->setCommand(PlayerActor::STATUS_INIT_POKER);
                $command->setArg(PokerCard::getPokerGroup(5));
                $pushData[$playerActorId] = $command;
            }
            PlayerActor::invoke()->fastPushMulti($pushData);
            //等待15s下注时间，
            \co::sleep(15);
            //告知本轮参与用户，进入冻结期，等待亮庄家牌结算
            $command = new Command();
            $command->setCommand(PlayerActor::STATUS_WAITING_COMPARE);
            $pushData = [];
            foreach ($this->roomPlayerList as $playerActorId){
                $pushData[$playerActorId] = $command;
            }
            PlayerActor::invoke()->fastPushMulti($pushData);
            //告知本轮参与用户，庄家牌，并在各自的playerActor中计算输赢结果,
            $command = new Command();
            $command->setCommand(PlayerActor::STATUS_COMPARE);
            $command->setArg($this->banker);
            $pushData = [];
            foreach ($this->roomPlayerList as $playerActorId){
                $pushData[$playerActorId] = $command;
            }
            PlayerActor::invoke()->fastPushMulti($pushData);
        });

    }

    function onMessage($msg)
    {
        // TODO: Implement onMessage() method.
    }

    function onExit($arg)
    {
        // TODO: Implement onExit() method.
    }

    protected function onException(\Throwable $throwable)
    {
        // TODO: Implement onException() method.
    }
}