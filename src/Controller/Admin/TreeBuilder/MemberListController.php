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
use Utils\StatusCode;

class MemberListController extends BaseAdminController {
    const userUpdateMemberImagesActions = "user_update_member_images";

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
            case "upload":
                $this->uploadFiles();
                break;
            case "removeImage":
                $memberId = $_POST["memberId"];
                $this->removeUploadedFile($memberId);
                $this->sendJsonResponse("Removing image for: $memberId");
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
            "partners" => $possiblePartners,
            "imageAction" => "/admin/tree_builder/members/upload"
        ]));
        $this->sendJsonResponse($response);
    }

    private function loadSelectedMember($id)
    {
        $member = $this->manager->getFamilyMemberDetails($id);

        if (is_null($member)) {
            $response = new Response(StatusCode::NOT_FOUND, "Member not found");
            $this->sendJsonResponse($response);
            return;
        }

        echo $this->sendJsonResponse($member);
    }

    private function updateSelectedMember($id)
    {
        if (!isset($_POST["member"])) {
            $response = new Response("Missing parameter member", StatusCode::UNPROCESSED_ENTITY);
            $this->sendJsonResponse($response);
            return;
        }

        $familyMember = $this->arrayToObject($_POST["member"], FamilyMember::class);

        $isUpdated = $this->manager->updateFamilyMember($id, $familyMember);

        $imagesChanged = $this->checkIfImagesChange($id);

        if (!$isUpdated && $imagesChanged) {
            $isUpdated = $imagesChanged;
        }

        $response = null;
        if ($isUpdated) {
            $response = new Response("Member updated", StatusCode::OK);
        } else {
            $response = new Response("No changes", StatusCode::NO_CONTENT);
        }

        $this->sendJsonResponse($response);
    }

    private function checkIfImagesChange($id)
    {
        $uploadedFiles = [];
        $removedFiles = [];
        $storeFolder = 'uploads';   //2
        $destFolder = $storeFolder . "/";

        foreach ($_SESSION[self::userUpdateMemberImagesActions] as $action) {
            if ($action->action == "add") {
                $targetFile = $destFolder . uniqid("member_iamge_") . ".jpg";

                if (rename($action->data, $targetFile)) {
                    $uploadedFiles[] = $targetFile;
                } else {
                    echo "Error occurred\n";
                }
            } else if ($action->action == "remove") {
                $removedFiles[] = $action->data;

                // TODO: remove image file from disk
            }
        }

        $isSucceed = false;

        if (count($uploadedFiles) > 0) {
            $isSucceed = $this->manager->insertMemberImage($id, $uploadedFiles);
        }

        if (count($removedFiles) > 0) {
            $isSucceed = $this->manager->removeMemberImage($id);
        }

        $_SESSION[self::userUpdateMemberImagesActions] = [];
        return $isSucceed;
    }

    private function uploadFiles()
    {
        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $storeFolder = 'uploads/temp';   //2
            $destFolder = $storeFolder . "/";
            $targetFile = $destFolder . uniqid("member_image_") . ".jpg";

            if (!file_exists($destFolder))
                mkdir($destFolder, 0x0777, true);

//            if (move_uploaded_file($tempFile, $targetFile)) {
            if ($this->changeImageQuality($tempFile, $targetFile, 90)) {
                $action = new \stdClass();
                $action->action = "add";
                $action->data = $targetFile;

                $_SESSION[self::userUpdateMemberImagesActions][] = $action;
            } else {
                echo "Error occurred\n";
            }
        }
    }

    private function changeImageQuality($tempFile, $targetFile, $restrainedQuality)
    {
        //open a stream for the uploaded image
        $streamHandle = @fopen($tempFile, 'r');
        //create a image resource from the contents of the uploaded image
        $resource = imagecreatefromstring(stream_get_contents($streamHandle));

        $isDone = false;

        if (!$resource)
            return $isDone;

        //close our file stream
        @fclose($streamHandle);

        //move the uploaded file with a lesser quality
        $isDone = imagejpeg($resource, $targetFile, $restrainedQuality);
        //delete the temporary upload
        @unlink($tempFile['tmp_name']);
        return $isDone;
    }

    private function removeUploadedFile($id)
    {
        $image = $this->manager->retrieveMemberImage($id);

        if (!is_null($image)) {
            $action = new \stdClass();
            $action->action = "remove";
            $action->data = $image->image;

            $_SESSION[self::userUpdateMemberImagesActions][] = $action;
        }
    }

    private function sendJsonResponse($data)
    {
        header("Content-type: Application/json");
        echo json_encode($data);
    }
}