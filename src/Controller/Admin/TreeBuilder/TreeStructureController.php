<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 22.12.2017
 * Time: 20:14
 */

namespace Controller\Admin\TreeBuilder;

use Controller\Admin\BaseAdminController;
use Database\FamilyManager;
use Model\FamilyMember;
use Model\Response;
use Utils\ImageFileHelper;
use Utils\StatusCode;

class TreeStructureController extends BaseAdminController {

    const userEditMemberImageActions = "user_edit_member_images";

    /**
     * @var FamilyManager
     */
    private $manager;
    private $imageFileHelper;

    /**
     * @var MemberEditController
     */
    private $memberEditController;

    /**
     * TreeStructureController constructor.
     */
    public function __construct()
    {
        $this->manager = new FamilyManager();

        $this->imageFileHelper = new ImageFileHelper();

        $this->memberEditController = new MemberEditController();
        $this->memberEditController->setManager($this->manager);
        $this->memberEditController->setImagesKey(self::userEditMemberImageActions);
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
            $this->memberEditController->updateSelectedMember($_GET["id"]);
        } elseif ($action == "getMembers") {
            if (isset($_GET["id"])) {
                $this->loadSelectedMember($_GET["id"]);
            } else {
                $this->loadFamilyMembers();
            }
        } elseif ($action == "upload") {
            $this->memberEditController->uploadFiles();
        } elseif ($action == "removeImage" && isset($_POST["memberId"])) {
            $this->memberEditController->removeUploadedFile($_POST["memberId"]);
        } elseif ($action == "removeTempImage") {
            $this->memberEditController->removeTemporaryUploadedFile();
        } else {
            $statusCode = StatusCode::NOT_FOUND;
            $response = new Response(StatusCode::getMessageForCode($statusCode), $statusCode);
            $this->sendJsonResponse($response);
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
            $response = new Response("Required family name", StatusCode::UNPROCESSED_ENTITY);

            header("Content-type: Application/json");
            echo json_encode($response);
            return;
        }

        $familyTreeName = $_POST["familyName"];
        $this->manager->addFamily($familyTreeName);

        $response = new Response("Saving succeed", StatusCode::OK);
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
            $response = new Response(StatusCode::NOT_FOUND, $this->translate("admin-edit-member-not-found"));
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

        $familyMember = $this->manager->getFamilyMemberDetails($_POST["member"]);

        $familyId = $_SESSION["selectedFamily"];
        $members = $this->manager->loadFamily($familyId);
        $possiblePartners = $this->manager->loadPossiblePartners($familyId, intval($familyMember->id));

        $response = array("template" => $this->render("/admin/trees/tree_builder_edit_member.html.twig", [
            "selectedMember" => $familyMember,
            "members" => $members,
            "partners" => $possiblePartners,
            "imageAction" => "/admin/tree_builder/upload"
        ]));
        $this->sendJsonResponse($response);
    }
}