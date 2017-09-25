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
    public function indexAction() {
        if(!isset($_SESSION['token']) || empty($_SESSION['token'])){
            header("location: admin/login");
            exit;
        }

        $this->indexCustomAction();
    }

    protected abstract function indexCustomAction();
}