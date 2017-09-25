<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 18:52
 */

namespace Controller\Admin;

session_start();

class PanelController extends BaseAdminController
{
    protected function indexCustomAction()
    {
        echo $this->render("/admin/panel/panel.html.twig");
    }
}