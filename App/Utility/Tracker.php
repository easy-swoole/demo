<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/27
 * Time: 8:48 PM
 */

namespace App\Utility;


use EasySwoole\Component\Singleton;
use EasySwoole\Trace\TrackerManager;

class Tracker extends TrackerManager
{
    use Singleton;
}