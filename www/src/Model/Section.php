<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 28.09.2017
 * Time: 22:16
 */

namespace Model;


use JsonSerializable;

class Section implements JsonSerializable
{
    public $id;
    public $name;

    function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "name" => $this->name
        ];
    }


}