<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 25.12.2017
 * Time: 02:35
 */

namespace Controller\Admin\TreeBuilder;

use Controller\Admin\BaseAdminController;
use Database\FamilyManager;
use Model\FamilyMember;
use Model\Response;

//TODO: Move code responsible for managing new member scene in to this controller
class NewMemberController extends BaseAdminController {
    private $manager;

    /**
     * NewMemberController constructor.
     */
    public function __construct()
    {
        $this->manager = new FamilyManager();
    }


    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        if ($action == null) {
            $this->viewAddNewMember();
        } elseif ($action == "save") {
            $this->addMemberToDB();
        }

    }

    private function viewAddNewMember()
    {
        if (!isset($_SESSION["selectedFamily"])) {
            return;
        }

        $familyId = $_SESSION["selectedFamily"];
        $members = $this->manager->loadFamily($familyId);

        echo $this->render("/admin/trees/tree_builder_new_member.html.twig", [
            "userLogged" => $this->userLogged,
            "familyMembers" => $members
        ]);
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