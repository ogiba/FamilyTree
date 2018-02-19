<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 19.02.2018
 * Time: 20:17
 */

namespace Model;


class FileAction {
    /**
     * @var string
     */
    public $action;
    /**
     * @var string
     */
    public $data;

    /**
     * FileAction constructor.
     * @param string $action
     * @param string $data
     */
    public function __construct($action, $data)
    {
        $this->action = $action;
        $this->data = $data;
    }
}