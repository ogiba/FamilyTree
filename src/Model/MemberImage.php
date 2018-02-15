<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 15.02.2018
 * Time: 21:29
 */

namespace Model;


use JsonSerializable;

class MemberImage implements JsonSerializable {

    public $image;
    public $memberId;
    public $size;

    public function jsonSerialize()
    {
        return [
            "image" => $this->image,
            "memberId" => $this->memberId,
            "size" => $this->size
        ];
    }

}