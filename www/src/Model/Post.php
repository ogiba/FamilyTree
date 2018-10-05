<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:34
 */

namespace Model;


use JsonSerializable;

class Post implements JsonSerializable
{
    public $id;
    public $title;
    public $shortDescription;
    public $content;
    public $author;
    public $modifiedBy;
    public $published;
    public $images;
    public $timeStamp;

    /**
     * Post constructor.
     * @param $title
     * @param $content
     * @param $dateTime
     */
    public function __construct($title, $content, $dateTime)
    {
        $this->title = $title;
        $this->content = $content;
        $this->timeStamp = $dateTime;
    }

    function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "shortDescription" => $this->shortDescription,
            "content" => $this->content,
            "author" => $this->author,
            "modifiedBy" => $this->modifiedBy,
            "published" => $this->published,
            "timeStamp" => $this->timeStamp
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }



    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->timeStamp;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }


}