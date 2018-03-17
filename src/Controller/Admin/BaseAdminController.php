<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 19:18
 */

namespace Controller\Admin;


use Controller\BaseController;

abstract class BaseAdminController extends BaseController {
    protected $userLogged = false;

    public function action($name, $action, $params)
    {
        $_SESSION['url'] = $_SERVER['REQUEST_URI'];

        if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
            header("location: /admin/login");
            exit;
        }

        $this->userLogged = true;
    }

    protected function arrayToObject(array $array, $className)
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(serialize($array), ':')
        ));
    }

    function objectToObject($instance, $className)
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($className),
            $className,
            strstr(strstr(serialize($instance), '"'), ':')
        ));
    }
}