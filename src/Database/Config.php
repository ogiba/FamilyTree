<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 01:42
 */

namespace Database;


use JsonSerializable;

class Config implements JsonSerializable
{
    public $host;
    public $login;
    public $pw;
    public $table;

    function jsonSerialize()
    {
        return [
            "host" => $this->host,
            "login" => $this->login,
            "pw" => $this->pw,
            "table" => $this->table
        ];
    }


}