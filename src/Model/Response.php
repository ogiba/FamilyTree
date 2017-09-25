<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 22:04
 */

namespace Model;


use JsonSerializable;

class Response implements JsonSerializable
{
    private $message;
    private $statusCode;

    /**
     * Response constructor.
     * @param $message
     * @param $statusCode
     */
    public function __construct($message, $statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    function jsonSerialize()
    {
        return [
            "message" => $this->message,
            "statusCode" => $this->statusCode
        ];
    }


}