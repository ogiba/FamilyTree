<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 20.12.2017
 * Time: 22:04
 */

namespace Model;


use JsonSerializable;

class TreeResponse implements JsonSerializable
{
    private $family;
    private $memberTemplate;
    private $pairTemplate;

    /**
     * TreeResponse constructor.
     * @param $family
     * @param $memberTemplate
     * @param $pairTemplate
     */
    public function __construct($family, $memberTemplate, $pairTemplate)
    {
        $this->family = $family;
        $this->memberTemplate = $memberTemplate;
        $this->pairTemplate = $pairTemplate;
    }

    function jsonSerialize()
    {
        return [
            "family" => $this->family,
            "memberTemplate" => $this->memberTemplate,
            "pairTemplate" => $this->pairTemplate
        ];
    }

}