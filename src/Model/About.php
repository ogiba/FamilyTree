<?php
/**
 * User: ogiba
 * Date: 16.08.2017
 * Time: 21:50
 */

namespace Model;


class About
{
    private $desc;
    private $image;

    /**
     * About constructor.
     * @param $desc
     */
    public function __construct($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
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


}