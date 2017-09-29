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
            if (!empty($params)) {
                $this->loadSectionPreview($userLogged, $params[0]);
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
}