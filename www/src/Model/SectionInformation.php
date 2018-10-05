<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 20:03
 */

namespace Model;

use JsonSerializable;

class SectionInformation implements JsonSerializable
{
    public $id;
    public $content;
    public $image;
    public $section;
    public $dateTime;

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
     * @param string $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }


}