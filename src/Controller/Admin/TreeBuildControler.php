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
            $this->viewIndex();
        }
    }

    private function viewIndex()
    {
        echo $this->render("/admin/trees/trees_view.html.twig");
    }
}