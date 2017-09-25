<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 19:00
 */

namespace Controller\Admin;


use Controller\BaseController;

class LoginController extends BaseController
{
    public function indexAction() {
        echo $this->render("admin/login.html.twig");
    }
}