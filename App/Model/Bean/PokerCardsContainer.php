<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 22:02
 */

namespace App\Model\Bean;


class PokerCardsContainer
{
    /*
     * 用户手持牌
     */

    private $list = [];

    function addPoker(PokerCard $pokerCard):bool
    {
        if(count($this->list) == 5){
            return false;
        }
        $this->list[$pokerCard->getValue()] = $pokerCard;
        return true;
    }

    function getAllPoker():array
    {
        return $this->list;
    }

    function clear()
    {
        $this->list = [];
    }

    /*
     * 快速和另外一组牌比大小
     */
    function compare(PokerCardsContainer $container)
    {

    }
}