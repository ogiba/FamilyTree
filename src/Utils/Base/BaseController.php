<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 26.05.2018
 * Time: 02:24
 */

namespace Utils\Base;

abstract class BaseController {
    /**
     * @param string|null $name
     * @param string|null $action
     * @param array $params
     */
    public abstract function action($name, $action, $params);
}