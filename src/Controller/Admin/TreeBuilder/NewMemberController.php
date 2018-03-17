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
use Utils\ImageFileHelper;
use Utils\StatusCode;

class NewMemberController extends BaseAdminController {
    private $manager;
    private $imageFileHelper;

    const userAddMemberImagesActions = "user_add_member_images";

    /**
     * NewMemberController constructor.
     */
    public function __construct()
    {
        $this->manager = new FamilyManager();
        $this->imageFileHelper = new ImageFileHelper();
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
        } elseif ($action == "removeTempImage") {
            $this->removeTemporaryUploadedFile();
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
            $response = new Response($this->translate("missing-param"), StatusCode::UNPROCESSED_ENTITY);
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
            $message = $this->translate("admin-new-member-added");
            $statusCode = StatusCode::OK;
        } else {
            $message = $this->translate("admin-new-member-adding-failed");
            $statusCode = StatusCode::INTERNAL_SERVER_ERROR;
        }

        $response = new Response($message, $statusCode);
        $this->sendJsonResponse($response);
    }

    private function checkIfImagesChange($id)
    {
        $filteredAction = $this->imageFileHelper->checkAction($_SESSION[self::userAddMemberImagesActions], "uploads", "member_iamge_");

        $isSucceed = false;

        if (count($filteredAction->uploaded) > 0) {
            $isSucceed = $this->manager->insertMemberImage($id, $filteredAction->uploaded);
        }

        $_SESSION[self::userAddMemberImagesActions] = [];
        return $isSucceed;
    }

    private function uploadFiles()
    {
        $resolvedAction = $this->imageFileHelper->uploadFiles($_FILES, "uploads/temp", "member_image_", 90);

        if (!is_null($resolvedAction)) {
            $_SESSION[self::userAddMemberImagesActions][] = $resolvedAction;
        }
    }

    private function removeTemporaryUploadedFile()
    {
        $isSucceed = $this->imageFileHelper->removeTempFiles($_SESSION[self::userAddMemberImagesActions]);

        $response = null;
        if ($isSucceed)
            $response = new Response($this->translate("admin-edit-member-uploaded-image-removed"), StatusCode::OK);
        else
            $response = new Response($this->translate("admin-edit-member-cannot-remove-img"), StatusCode::UNPROCESSED_ENTITY);

        $this->sendJsonResponse($response);
    }
}