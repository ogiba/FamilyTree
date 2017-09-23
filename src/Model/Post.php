<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:34
 */

namespace Model;


class Post
{
    private $title;
    private $content;
    private $images;
    private $dateTime;

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
        $this->dateTime = $dateTime;
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
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }


}