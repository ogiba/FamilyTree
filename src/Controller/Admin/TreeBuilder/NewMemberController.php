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
        $insertResult = $this->manager->insertNewMember($familyId, $newMember);

        $imagesChanged = $this->checkIfImagesChange($insertResult->memberId);

        $message = null;
        $statusCode = null;
        if ($insertResult->isSucceed) {
            $message = "New member inserted";
            $statusCode = StatusCode::OK;
        } else {
            $message = "Adding new member failed";
            $statusCode = StatusCode::INTERNAL_SERVER_ERROR;
        }

        $response = new Response($message, $statusCode);
        $this->sendJsonResponse($response);
    }

    private function checkIfImagesChange($id)
    {
        $uploadedFiles = [];
        $removedFiles = [];
        $storeFolder = 'uploads';   //2
        $destFolder = $storeFolder . "/";

        foreach ($_SESSION[self::userAddMemberImagesActions] as $action) {
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

        $_SESSION[self::userAddMemberImagesActions] = [];
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

                $_SESSION[self::userAddMemberImagesActions][] = $action;
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

    private function sendJsonResponse($data)
    {
        header("Content-type: Application/json");
        echo json_encode($data);
    }
}