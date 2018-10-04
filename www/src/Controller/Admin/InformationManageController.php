<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 28.09.2017
 * Time: 22:11
 */

namespace Controller\Admin;


use Database\InformationManager;

class InformationManageController extends BaseAdminViewController {
    const userSectionImagesActions = "user-section-images-actions";
    private $manager;

    /**
     * InformationManageController constructor.
     */
    public function __construct()
    {
        $this->manager = new InformationManager();
    }


    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        $userLogged = $this->checkIfUserLogged();

        if (is_null($name)) {
            $this->prepareSectionsList($userLogged);
        } else if ($name == "section" && $action == "view") {
            if (!empty($params) && count($params) == 1) {
                $this->loadSectionPreview($userLogged, $params[0]);
            } else if (!empty($params) && $params[1] == "edit") {
                $this->loadSectionEdit($userLogged, $params[0]);
            } else if (!empty($params) && $params[1] == "save") {
                $this->saveChangesInSection($params[0]);
            } else if (!empty($params) && $params[1] == "upload") {
                $this->uploadSectionFile($params[0]);
            }
        }
    }

    private function prepareSectionsList($userLogged = false)
    {
        $sections = $this->loadSectionsFormDB();

        echo $this->render("/admin/section/sections.html.twig", [
            "userLogged" => $userLogged,
            "sections" => $sections
        ]);
    }

    private function loadSectionsFormDB()
    {
        return $this->manager->loadSections();
    }

    private function loadSectionPreview($userLogged, $id)
    {
        $sectionInformation = $this->manager->loadSectionById($id);

        echo $this->render("/admin/section/section_view.html.twig", [
            "userLogged" => $userLogged,
            "section" => $sectionInformation
        ]);
    }

    private function loadSectionEdit($userLogged, $id)
    {
        $sectionToEdit = $this->manager->loadSectionById($id);
        $availableSections = $this->loadSectionsFormDB();

        $_SESSION[self::userSectionImagesActions] = [];

        echo $this->render("/admin/section/section_edit.html.twig", [
            "userLogged" => $userLogged,
            "section" => $sectionToEdit,
            "sections" => $availableSections
        ]);
    }

    private function saveChangesInSection($id)
    {
        if (!isset($_POST["content"]) || empty($_POST["content"])) {
            exit;
        }

        $updatedContent = $_POST["content"];

        $this->manager->updateSection($id, $updatedContent);

        // TODO: combine with uploading images in PostController and make common interface

        $uploadedFiles = [];
        $removedFiles = [];
        $storeFolder = 'uploads';   //2
        $destFolder = $storeFolder . "/";

        foreach($_SESSION[self::userSectionImagesActions] as $action)
        {
            if($action->action == "add")
            {
                $targetFile = $destFolder . uniqid("section_iamge_");

                if (rename($action->data, $targetFile))
                {
                    $uploadedFiles[] = $targetFile;
                }
                else
                {
                    echo "Error occurred\n";
                }
            }
            else if($action->action == "remove")
            {
                $removedFiles[] = $action->data;

                // TODO: remove image file from disk
            }
        }

        $this->manager->insertSectionImages($id, $uploadedFiles);

        // TODO: remove images from database
        //$this->manager->removeSectionImages($id, $removedFiles);
    }

    private function uploadSectionFile($id)
    {
        if (!empty($_FILES))
        {
            $tempFile = $_FILES['file']['tmp_name'];
            $storeFolder = 'uploads/temp';   //2
            $destFolder = $storeFolder . "/";
            $targetFile = $destFolder . uniqid("section_iamge_");

            if(!file_exists($destFolder))
                mkdir($destFolder,0x0777, true);

            if (move_uploaded_file($tempFile, $targetFile))
            {
                $action = new \stdClass();
                $action->action = "add";
                $action->data = $targetFile;

                $_SESSION[self::userSectionImagesActions][] = $action;
            }
            else
            {
                echo "Error occurred\n";
            }
        }
    }
}