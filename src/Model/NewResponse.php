<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 13.03.2018
 * Time: 19:46
 */

namespace Model;

use JsonSerializable;

class NewResponse implements JsonSerializable {

    /**
     * @var string
     */
    private $message;
    /**
     * @var integer
     */
    private $statusCode;
    private $content;

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
            "statusCode" => $this->statusCode,
            "content" => $this->content
        ];
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}