<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 17:12
 */

namespace Model;


use JsonSerializable;

class FamilyMember implements JsonSerializable
{
    public $id;
    public $firstName;
    public $lastName;
    public $maidenName;
    public $birthDate;
    public $deathDate;
    public $parent;
    public $children;
    public $image;
    public $family;
    public $timeStamp;

    /**
     * FamilyMember constructor.
     * @param $firstName
     * @param $lastName
     * @param $family
     */
    public function __construct($firstName, $lastName, $family)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->family = $family;
    }

    function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "maidenName" => $this->maidenName,
            "birthDate" => $this->birthDate,
            "deathDate" => $this->deathDate,
            "parent" => $this->parent,
            "children" => $this->children,
            "image" => $this->image,
            "family" => $this->family,
            "timeStamp" => $this->timeStamp
        ];
    }

}