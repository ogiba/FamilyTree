<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 28.09.2017
 * Time: 22:11
 */

namespace Controller\Admin;


use Database\InformationManager;

class InformationManageController extends BaseAdminController {
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

        if (is_null($name)) {
            $this->prepareSectionsList($userLogged);
        } else if ($name == "section" && $action == "view") {
            if (!empty($params) && count($params) == 1) {
                $this->loadSectionPreview($userLogged, $params[0]);
            } else if(!empty($params) && $params[1] == "edit") {
                $this->loadSectionEdit($userLogged, $params[0]);
            }
        }
    }

    private function prepareSectionsList($userLogged = false)
    {
        $sections = $this->loadSectionsFormDB();

        echo $this->render("/admin/section/sections.html.twig", [
            "userLogged" => $userLogged,
            "sections" => $sections
        ]);
    }

    private function loadSectionsFormDB()
    {
        return $this->manager->loadSections();
    }

    private function loadSectionPreview($userLogged, $id)
    {
        $sectionInformation = $this->manager->loadSectionById($id);

        echo $this->render("/admin/section/section_view.html.twig", [
           "userLogged" => $userLogged,
           "section" => $sectionInformation
        ]);
    }

    private function loadSectionEdit($userLogged, $id) {
        $sectionToEdit = $this->manager->loadSectionById($id);

        echo $this->render("/admin/section/section_edit.html.twig", [
            "userLogged" => $userLogged,
            "section" => $sectionToEdit
        ]);
    }
}