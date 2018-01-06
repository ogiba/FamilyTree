<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 06.01.2018
 * Time: 23:42
 */

namespace Model;


use JsonSerializable;

class TreeMemberResponse implements JsonSerializable {
    public $member;
    public $template;

    /**
     * TreeMemberResponse constructor.
     * @param $member
     * @param $template
     */
    public function __construct($member, $template)
    {
        $this->member = $member;
        $this->template = $template;
    }


    public function jsonSerialize()
    {
        return [
            "member" => $this->member,
            "template" => $this->template
        ];
    }

}