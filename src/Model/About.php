<?php
/**
 * User: ogiba
 * Date: 16.08.2017
 * Time: 21:50
 */

namespace Model;


use JsonSerializable;

class About implements JsonSerializable
{
    public $id;
    public $content;
    public $image;
    public $section;
    public $dateTime;


    /**
     * About constructor.
     * @param $desc
     */
    public function __construct($desc)
    {
        $this->content = $desc;
    }

    function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "content" => $this->content,
            "image" => $this->content,
            "section" => $this->section,
            "dateTime" => $this->dateTime
        ];
    }


    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->content;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @param mixed $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }
}