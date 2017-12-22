<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 22.12.2017
 * Time: 20:14
 */

namespace Controller\Admin;


use Database\FamilyManager;
use Model\Response;

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
                $this->viewIndex();
            } else {
                $this->viewInitialScreen();
            }
        } elseif ($action == "save") {
            $this->saveFamilyToDB();
        } elseif ($action == "getMembers") {
            $this->loadFamilyMembers();
        }
    }

    private function viewInitialScreen()
    {
        echo $this->render("/admin/trees/tree_builder_init_scene.html.twig", [
            "userLogged" => $this->userLogged
        ]);
    }

    private function viewIndex()
    {
        $selectedFamily = $this->loadFamilyData();

        $_SESSION["selectedFamily"] = $selectedFamily->id;

        echo $this->render("/admin/trees/tree_builder.html.twig", [
            "userLogged" => $this->userLogged,
            "family" => $selectedFamily
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

    private function saveFamilyToDB()
    {
        if (!isset($_POST["familyName"])) {
            $response = new Response("Required family name", 422);

            header("Content-type: Application/json");
            echo json_encode($response);
            return;
        }

        $familyTreeName = $_POST["familyName"];
        $this->manager->addFamily($familyTreeName);

        $response = new Response("Saving succeed", 200);
        $this->sendJsonResponse($response);
    }

    /**
     * Loads available family
     *
     * @return \Model\Family|null
     */
    private function loadFamilyData()
    {
        $families = $this->manager->loadFamilies();

        $numberOfFamilies = count($families);
        $availableFamily = null;
        if ($numberOfFamilies > 0) {
            $availableFamily = $families[$numberOfFamilies - 1];
        }

        return $availableFamily;
    }

    private function loadFamilyMembers()
    {
        if (!isset($_SESSION["selectedFamily"])) {

            header("Content-type: Application/json");
            $this->sendJsonResponse("");
            return;
        }

        $familyID = $_SESSION["selectedFamily"];

        $familyMembers = $this->manager->loadFamilyMembers($familyID);

        $this->sendJsonResponse($familyMembers);
    }

    private function sendJsonResponse($data)
    {
        header("Content-type: Application/json");
        echo json_encode($data);
    }
}