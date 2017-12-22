<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 22.12.2017
 * Time: 20:14
 */

namespace Controller\Admin;


use Database\FamilyManager;

class TreeBuildControler extends BaseAdminController
{

    private $manager;

    /**
     * TreeBuildControler constructor.
     */
    public function __construct()
    {
        $this->manager = new FamilyManager();
    }

    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        if ($action == null) {
            if ($this->checkIsFamilyExisiting()) {
                $this->viewInitialScreen();
            } else {
                $this->viewIndex();
            }
        }
    }

    private function viewInitialScreen() {
        echo $this->render("/admin/trees/tree_builder_init_scene.html.twig", [
            "userLogged" => $this->userLogged
        ]);
    }

    private function viewIndex()
    {
        echo $this->render("/admin/trees/tree_builder.html.twig", [
            "userLogged" => $this->userLogged
        ]);
    }

    private function checkIsFamilyExisiting()
    {
        $families = $this->manager->loadFamilies();

        if (count($families) > 0) {
            return true;
        }

        return false;
    }
}