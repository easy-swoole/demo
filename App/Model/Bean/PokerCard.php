<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 21:44
 */

namespace App\Model\Bean;


use EasySwoole\Spl\SplEnum;

class PokerCard extends SplEnum
{

    const BLACK_JOKER = 53;//黑鬼
    const RED_JOKER = 54;//红鬼
    /*
     * SPADE 黑桃
     * HEART 红桃
     * CLUB 梅花
     * DIAMOND 方块
     */

    const SPADE_ACE = 1;
    const SPADE_TWO = 2;
    const SPADE_THREE = 3;
    const SPADE_FOUR = 4;
    const SPADE_FIVE = 5;
    const SPADE_SIX = 6;
    const SPADE_SEVEN = 7;
    const SPADE_EIGHT = 8;
    const SPADE_NINE = 9;
    const SPADE_TEN = 10;
    const SPADE_JACK = 11;
    const SPADE_QUEEN = 12;
    const SPADE_KING = 13;

    const HEART_ACE = 14;
    const HEART_TWO = 15;
    const HEART_THREE = 16;
    const HEART_FOUR = 17;
    const HEART_FIVE = 18;
    const HEART_SIX = 19;
    const HEART_SEVEN = 20;
    const HEART_EIGHT = 21;
    const HEART_NINE = 22;
    const HEART_TEN = 23;
    const HEART_JACK = 24;
    const HEART_QUEEN = 25;
    const HEART_KING = 26;

    const CLUB_ACE = 27;
    const CLUB_TWO = 28;
    const CLUB_THREE = 29;
    const CLUB_FOUR = 30;
    const CLUB_FIVE = 31;
    const CLUB_SIX = 32;
    const CLUB_SEVEN = 33;
    const CLUB_EIGHT = 34;
    const CLUB_NINE = 35;
    const CLUB_TEN = 36;
    const CLUB_JACK = 37;
    const CLUB_QUEEN = 38;
    const CLUB_KING = 39;

    const DIAMOND_ACE = 40;
    const DIAMOND_TWO = 41;
    const DIAMOND_THREE = 42;
    const DIAMOND_FOUR = 43;
    const DIAMOND_FIVE = 44;
    const DIAMOND_SIX = 45;
    const DIAMOND_SEVEN = 46;
    const DIAMOND_EIGHT = 47;
    const DIAMOND_NINE = 48;
    const DIAMOND_TEN = 49;
    const DIAMOND_JACK = 50;
    const DIAMOND_QUEEN = 51;
    const DIAMOND_KING = 52;
}