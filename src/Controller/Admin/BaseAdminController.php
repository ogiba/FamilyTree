<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 19:18
 */

namespace Controller\Admin;


use Controller\BaseController;

abstract class BaseAdminController extends BaseController
{
    public function action($name, $action, $params)
    {
        $_SESSION['url'] = $_SERVER['REQUEST_URI'];

        if(!isset($_SESSION['token']) || empty($_SESSION['token'])){
            header("location: /admin/login");
            exit;
        }
    }
}