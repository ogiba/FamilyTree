<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 28.12.2017
 * Time: 02:28
 */

namespace Controller\Admin\TreeBuilder;


use Controller\Admin\BaseAdminController;
use Database\FamilyManager;
use Model\FamilyMember;
use Model\Response;

class MemberListController extends BaseAdminController {
    private $manager;

    /**
     * MemberListController constructor.
     */
    public function __construct()
    {
        $this->manager = new FamilyManager();
    }


    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params); // TODO: Change the autogenerated stub

        switch ($action) {
            case "edit":
                $this->editSelectedMember();
                break;
            case "getMembers":
                if (isset($_GET["id"])) {
                    $this->loadSelectedMember($_GET["id"]);
                }
                break;
            case "update":
                if (isset($_GET["id"])) {

                    $this->updateSelectedMember($_GET["id"]);
                }
                break;
            default:
                $this->viewIndex();
                break;
        }
    }

    private function viewIndex()
    {
        if (!isset($_SESSION["selectedFamily"])) {
            return;
        }

        $selectedFamily = $this->loadFamilyData($_SESSION["selectedFamily"]);
        $members = $this->manager->loadFamily($selectedFamily->id);

        echo $this->render("/admin/trees/tree_builder_members_list.html.twig", [
            "userLogged" => $this->userLogged,
            "family" => $selectedFamily,
            "familyMembers" => $members
        ]);
    }

    private function loadFamilyData($familyId)
    {
        $families = $this->manager->loadFamilies();

        $availableFamily = null;
        foreach ($families as $key => $value) {
            if ($value->
                id == $familyId) {
                $availableFamily = $value;
                break;
            }
        }

        return $availableFamily;
    }

    private function editSelectedMember()
    {
        if (!isset($_POST["member"]) || !isset($_SESSION["selectedFamily"])) {
            return;
        }

//        $familyMember = $this->arrayToObject($_POST["member"], FamilyMember::class);
        $familyMember = $this->manager->getFamilyMemberDetails($_POST["member"]);

        $familyId = $_SESSION["selectedFamily"];
        $members = $this->manager->loadFamily($familyId);
        $possiblePartners = $this->manager->loadPossiblePartners($familyId, intval($familyMember->id));

        $response = array("template" => $this->render("/admin/trees/tree_builder_edit_member.html.twig", [
            "selectedMember" => $familyMember,
            "members" => $members,
            "partners" => $possiblePartners
        ]));
        $this->sendJsonResponse($response);
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

    private function sendJsonResponse($data)
    {
        header("Content-type: Application/json");
        echo json_encode($data);
    }
}