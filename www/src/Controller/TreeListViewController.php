<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 02.04.2018
 * Time: 01:05
 */

namespace Controller;


use Database\FamilyManager;

class TreeListViewController extends BaseViewController {

    /**
     * @var FamilyManager
     */
    private $manager;

    /**
     * TreeListController constructor.
     */
    public function __construct()
    {
        $this->manager = new FamilyManager();
    }


    public function action($name, $action, $params)
    {
        switch ($action) {
            default:
                $this->indexAction();
                break;
        }
    }

    private function indexAction()
    {
        $params = array("userLogged" => $this->checkIfUserLogged(), "families" => $this->loadFamilies());
        echo $this->render("trees/tree_list.html.twig", $params);
    }

    private function loadFamilies()
    {
        $families = $this->manager->loadFamilies();
        return $families;
    }
}