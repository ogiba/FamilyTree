<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 18:52
 */

namespace Controller\Admin;


use Controller\BaseController;

session_start();

class PanelController extends BaseController
{
    public function indexAction(){
        if(!isset($_SESSION['token']) || empty($_SESSION['token'])){
            header("location: admin/login");
            exit;
        }

        echo $this->render("/admin/panel/panel.html.twig");
    }
}