<?php

namespace API;
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 01:26
 */
class SerializeManager
{

    public function serializeJson($data) {
        $jsonData = json_encode($data);
        return $jsonData;
    }
    public function deserializeJson($jsonData, $class) {
        $data = json_decode($jsonData,true);
        foreach ($data as $key => $value) $class->{$key} = $value;
        return $data;
    }
}