<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 17:06
 */

namespace Model;


use JsonSerializable;

class Family implements JsonSerializable
{
    public $id;
    public $familyName;
    public $timeStamp;

    /**
     * Family constructor.
     * @param $familyName
     */
    public function __construct($familyName)
    {
        $this->familyName = $familyName;
    }


    function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "familyName" => $this->familyName,
            "timeStamp" => $this->timeStamp
        ];
    }
}