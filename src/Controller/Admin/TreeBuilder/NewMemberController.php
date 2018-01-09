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
use Utils\StatusCode;

class NewMemberController extends BaseAdminController {
    private $manager;

    const userAddMemberImagesActions = "user_add_member_images";

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
        } elseif ($action == "upload") {
            $this->uploadFiles();
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
            $response = new Response("Missing required parameter", StatusCode::UNPROCESSED_ENTITY);
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
            $statusCode = StatusCode::OK;
        } else {
            $message = "Adding new member failed";
            $statusCode = StatusCode::INTERNAL_SERVER_ERROR;
        }

        $response = new Response($message, $statusCode);
        $this->sendJsonResponse($response);
    }

    private function uploadFiles()
    {
        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $storeFolder = 'uploads/temp';   //2
            $destFolder = $storeFolder . "/";
            $targetFile = $destFolder . uniqid("member_image_");

            if (!file_exists($destFolder))
                mkdir($destFolder, 0x0777, true);

            if (move_uploaded_file($tempFile, $targetFile)) {
                $action = new \stdClass();
                $action->action = "add";
                $action->data = $targetFile;

                $_SESSION[self::userAddMemberImagesActions][] = $action;
            } else {
                echo "Error occurred\n";
            }
        }
    }

    private function sendJsonResponse($data)
    {
        header("Content-type: Application/json");
        echo json_encode($data);
    }
}