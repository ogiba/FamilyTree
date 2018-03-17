<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 10.03.2018
 * Time: 13:14
 */

namespace Controller\Admin\TreeBuilder;


use Controller\Admin\BaseAdminController;
use Database\FamilyManager;
use Model\FamilyMember;
use Model\NewResponse;
use Utils\ImageFileHelper;
use Utils\StatusCode;

class MemberEditController extends BaseAdminController {
    const userEditMemberImages = "user_edit_member_images";

    /**
     * @var boolean
     */
    private $removingEnabled;

    /**
     * @var FamilyManager
     */
    private $manager;

    /**
     * @var ImageFileHelper
     */
    private $imageFileHelper;

    /**
     * @var string
     */
    private $imagesKey;

    /**
     * MemberEditController constructor.
     */
    public function __construct()
    {
        $this->removingEnabled = false;
        $this->manager = new FamilyManager();
        $this->imageFileHelper = new ImageFileHelper();
        $this->imagesKey = self::userEditMemberImages;
    }

    public function updateSelectedMember($id)
    {
        if (!isset($_POST["member"])) {
            $response = new NewResponse($this->translate("admin-edit-member-failed-to-update"), StatusCode::UNPROCESSED_ENTITY);
            $this->sendJsonNewResponse($response);
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
            $response = new NewResponse($this->translate("admin-edit-member-updated"), StatusCode::OK);
        } else {
            $response = new NewResponse($this->translate("admin-edit-member-no-changes"), StatusCode::NO_CONTENT);
        }

        $this->sendJsonNewResponse($response);
    }

    private function checkIfImagesChange($id)
    {
        $filteredFiles = $this->imageFileHelper->checkAction($_SESSION[$this->imagesKey],
            "uploads", "member_image_");

        $isSucceed = false;

        if (count($filteredFiles->uploaded) > 0) {
            $isSucceed = $this->manager->insertMemberImage($id, $filteredFiles->uploaded);
        }

        if (count($filteredFiles->removed) > 0) {
            $isSucceed = $this->manager->removeMemberImage($id);

            if ($isSucceed) {
                $this->imageFileHelper->removeFiles($filteredFiles->removed);
            }
        }

        $_SESSION[$this->imagesKey] = [];
        return $isSucceed;
    }

    public function uploadFiles()
    {
        $receivedAction = $this->imageFileHelper->uploadFiles($_FILES, "uploads/temp",
            "member_image_", 90);

        if (!is_null($receivedAction)) {
            $_SESSION[$this->imagesKey][] = $receivedAction;
        }
    }

    public function removeUploadedFile($id)
    {
        $image = $this->manager->retrieveMemberImage($id);

        if (!is_null($image)) {
            $preparedAction = $this->imageFileHelper->prepareAction($image->image, "remove");

            $_SESSION[$this->imagesKey][] = $preparedAction;
        }

        $response = new NewResponse("Removing image for: $id", StatusCode::OK);
        $this->sendJsonNewResponse($response);
    }

    public function removeTemporaryUploadedFile()
    {
        $isSucceed = $this->imageFileHelper->removeTempFiles($_SESSION[$this->imagesKey]);

        $response = null;
        if ($isSucceed)
            $response = new NewResponse($this->translate("admin-edit-member-uploaded-image-removed"), StatusCode::OK);
        else
            $response = new NewResponse($this->translate("admin-edit-member-cannot-remove-img"), StatusCode::UNPROCESSED_ENTITY);

        $this->sendJsonNewResponse($response);
    }

    public function removeMember()
    {
        if (!isset($_POST["memberId"])) {
            exit();
        }

        $memberId = $_POST["memberId"];

        $isSucceed = $this->manager->removeMember(intval($memberId));

        if ($isSucceed)
            $response = new NewResponse($this->translate("admin-edit-member-removed"), StatusCode::OK);
        else
            $response = new NewResponse($this->translate("admin-edit-member-cannot-remove"), StatusCode::UNPROCESSED_ENTITY);

        $this->sendJsonNewResponse($response);
    }

    /**
     * @param bool $removingEnabled
     */
    public function setRemovingEnabled($removingEnabled)
    {
        $this->removingEnabled = $removingEnabled;
    }

    /**
     * @param FamilyManager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ImageFileHelper $imageFileHelper
     */
    public function setImageFileHelper($imageFileHelper)
    {
        $this->imageFileHelper = $imageFileHelper;
    }

    /**
     * @param string $imagesKey
     */
    public function setImagesKey($imagesKey)
    {
        $this->imagesKey = $imagesKey;
    }


}