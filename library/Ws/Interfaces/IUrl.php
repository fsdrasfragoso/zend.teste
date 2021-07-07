<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/22/16
 * Time: 6:08 PM
 */
interface Ws_Interfaces_IUrl
{
    static function makeUrl($identifier);
    static function makeParams($identifier);
}