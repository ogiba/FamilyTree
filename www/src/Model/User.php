<?php
/**
 * User: ogiba
 * Date: 16.08.2017
 * Time: 21:24
 */

namespace Model;

use JsonSerializable;

class User implements JsonSerializable {
    public $id;
    public $nickName;
    public $email;
    public $firstName;
    public $lastName;
    public $image;
    public $userType;

    function __construct($id, $nickName)
    {
        $this->id = $id;
        $this->nickName = $nickName;
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "nickName" => $this->nickName,
            "email" => $this->email,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "avatar" => $this->image,
            "userType" => $this->userType
        ];
    }
}