<?php

/**
 * Class Ws_Updater
 *
 * Atualiza em tempo real o CloudSearch
 *
 */

class Ws_Url implements Ws_Interfaces_IUrl {

    static function makeUrl ($identifier)
    {
        $arr = self::makeParams($identifier);

        $querie = http_build_query($arr);


        return static::WS_UPDATER.$querie;
    }


    static function makeParams($identifier)
    {
        $arr = array(
            'identifier' => $identifier,
            'base_type' => static::BASETYPE,
            'token' => static::ACCESS_TOKEN
        );

        return $arr;
    }


}