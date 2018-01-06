<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 31.12.2017
 * Time: 01:46
 */

namespace Model;


use JsonSerializable;

class ChildParentPair implements JsonSerializable {
    public $id;
    public $child;
    public $parent;

    public function jsonSerialize()
    {
        return [
            "child" => $this->child,
            "parent" => $this->parent
        ];
    }

    /**
     * @return mixed
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }


}