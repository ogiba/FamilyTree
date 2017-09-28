<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 28.09.2017
 * Time: 22:11
 */

namespace Controller\Admin;


use Database\InformationManager;

class InformationManageController extends BaseAdminController
{
    private $manager;

    /**
     * InformationManageController constructor.
     */
    public function __construct()
    {
        $this->manager = new InformationManager();
    }


    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        $sections = $this->loadSectionsFormDB();

        echo $this->render("/admin/section/sections.html.twig", [
            "userLogged" => $userLogged,
            "sections" => $sections
        ]);
    }

    private function loadSectionsFormDB() {
        return $this->manager->loadSections();
    }
}