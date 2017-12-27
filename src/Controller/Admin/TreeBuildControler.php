<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 22.12.2017
 * Time: 20:14
 */

namespace Controller\Admin;

use Database\FamilyManager;
use Model\FamilyMember;
use Model\Response;

class TreeBuildControler extends BaseAdminController {

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
        } elseif ($action == "save_family") {
            $this->saveFamilyToDB();
        } elseif ($action == "edit") {
            $this->editSelectedMember();
        } elseif ($action == "update" && isset($_GET["id"])) {
            $this->updateSelectedMember($_GET["id"]);
        } elseif ($action == "getMembers") {
            if (isset($_GET["id"])) {
                $this->loadSelectedMember($_GET["id"]);
            } else {
                $this->loadFamilyMembers();
            }
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
            $this->sendJsonResponse("");
            return;
        }

        $familyID = $_SESSION["selectedFamily"];

        $familyMembers = $this->manager->loadFamilyMembers($familyID);

        $this->sendJsonResponse($familyMembers);
    }

    private function loadSelectedMember($id)
    {
        $member = $this->manager->getFamilyMemberDetails($id);

        if (is_null($member)) {
            $response = new Response(400, "Member not found");
            $this->sendJsonResponse($response);
            return;
        }

        echo $this->sendJsonResponse($member);
    }

    private function editSelectedMember()
    {
        if (!isset($_POST["member"]) || !isset($_SESSION["selectedFamily"])) {
            return;
        }

        $familyMember = $this->arrayToObject($_POST["member"], FamilyMember::class);

        $familyId = $_SESSION["selectedFamily"];
        $members = $this->manager->loadFamily($familyId);
        $possiblePartners = $this->manager->loadPossiblePartners($familyId, intval($familyMember->id), intval($familyMember->parent));

        $response = array("template" => $this->render("/admin/trees/tree_builder_edit_member.html.twig", [
            "selectedMember" => $familyMember,
            "members" => $members,
            "partners" => $possiblePartners
        ]));
        $this->sendJsonResponse($response);
    }

    private function updateSelectedMember($id)
    {
        if (!isset($_POST["member"])) {
            $response = new Response("Missing parameter member", 422);
            $this->sendJsonResponse($response);
            return;
        }

        $familyMember = $this->arrayToObject($_POST["member"], FamilyMember::class);

        $isUpdated = $this->manager->updateFamilyMember($id, $familyMember);

        $response = null;
        if ($isUpdated) {
            $response = new Response("Member updated", 200);
        } else {
            $response = new Response("No changes", 204);
        }

        $this->sendJsonResponse($response);
    }

    public function addMemberToDB()
    {
        if (!isset($_POST["member"]) || !isset($_SESSION["selectedFamily"])) {
            $response = new Response("Missing required parameter", 422);
            $this->sendJsonResponse($response);
            return;
        }

        $newMember = $this->arrayToObject($_POST["member"], FamilyMember::class);
        $familyId = $_SESSION["selectedFamily"];
        $isSucceed = $this->manager->insertNewMember($familyId, $newMember);

        $message = null;
        $statusCode = null;
        if ($isSucceed) {
            $message = "New member inserted";
            $statusCode = 200;
        } else {
            $message = "Adding new member failed";
            $statusCode = 500;
        }

        $response = new Response($message, $statusCode);
        $this->sendJsonResponse($response);
    }

    private function sendJsonResponse($data)
    {
        header("Content-type: Application/json");
        echo json_encode($data);
    }
}