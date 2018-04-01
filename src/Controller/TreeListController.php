<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 02.04.2018
 * Time: 01:05
 */

namespace Controller;


class TreeListController extends BaseController {

    public function action($name, $action, $params)
    {
        switch ($action) {
            default:
                $this->indexAction();
                break;
        }
    }

    private function indexAction() {
        $params = array("userLogged" => $this->checkIfUserLogged());
        echo $this->render("trees/tree_list.html.twig", $params);
    }
}