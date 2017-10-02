<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 26.09.2017
 * Time: 22:19
 */

namespace Controller\Admin;


use Database\PostsManager;

class PostController extends BaseAdminController
{
    const userAddPostImagesActions = "user-add-post-images";
    private $manager;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->manager = new PostsManager();
    }

    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        if ($action == null) {
            $this->viewPostBehavior();
        } else if ($action == "edit") {
            $this->editPostBehavior($params);
        } else if ($action == "new") {
            $this->newPostBehavior($params);
        }
    }

    protected function indexCustomAction($path)
    {
        if ($path == null) {
            $this->viewPostBehavior();
        } else if ($path[2] == "edit") {
            $this->editPostBehavior($path);
        } else if ($path[2] == "new") {
            $this->newPostBehavior($path);
        }
    }

    private function viewPostBehavior()
    {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            header("location: /not_found");
            exit;
        }

        $postId = $_GET["id"];
        $postToView = $this->manager->loadPost($postId);

        echo $this->render("/admin/post/post_view.html.twig", [
            "userLogged" => $userLogged,
            "post" => $postToView
        ]);
    }

    private function editPostBehavior($pathArray)
    {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        if (count($pathArray) > 0 && $pathArray[0] == "update") {
            $this->updateEditedPost();
        } else {
            if (!isset($_GET["id"]) || empty($_GET["id"])) {
                header("location: /not_found");
                exit;
            }

            $postId = $_GET["id"];

            $post = $this->manager->loadPost($postId);

            $saveAction = "updatePost($postId)";

            echo $this->render("/admin/post/post_edit.html.twig", [
                "userLogged" => $userLogged,
                "post" => $post,
                "action" => $saveAction
            ]);
        }
    }

    private function newPostBehavior($pathArray)
    {
        if (count($pathArray) > 0 && $pathArray[0] == "upload") {
            $this->uploadFiles();
        } else if (count($pathArray) > 0 && $pathArray[0] == "save") {
            $this->saveNewPost();
        } else {
            $userLogged = false;
            if (isset($_SESSION["token"])) {
                $userLogged = true;
            }

            $saveAction = "savePost()";

            $_SESSION[self::userAddPostImagesActions] = [];

            echo $this->render("/admin/post/post_edit.html.twig", [
                "userLogged" => $userLogged,
                "action" => $saveAction
            ]);
        }
    }

    private function uploadFiles()
    {
        if (!empty($_FILES))
        {
            $tempFile = $_FILES['file']['tmp_name'];
            $storeFolder = 'uploads/temp';   //2
            $destFolder = $storeFolder . "/";
            $targetFile = $destFolder . uniqid("post_image_");

            if(!file_exists($destFolder))
                mkdir($destFolder,0x0777, true);

            if (move_uploaded_file($tempFile, $targetFile))
            {
                $action = new \stdClass();
                $action->action = "add";
                $action->data = $targetFile;

                $_SESSION[self::userAddPostImagesActions][] = $action;
            }
            else
            {
                echo "Error occurred\n";
            }
        }
    }

    private function saveNewPost()
    {
        if (!isset($_POST["title"]) || empty($_POST["title"]) || !isset($_POST["content"]) || empty($_POST["content"]) ||
            !($_POST["published"] === "false" || $_POST["published"] === "true")) {
            exit;
        }

        $postTitle = $_POST["title"];
        $postContent = $_POST["content"];
        $postPublished = $_POST["published"];

        $postId = $this->manager->savePost($postTitle, $postContent, $postPublished);

        $uploadedFiles = [];
        $storeFolder = 'uploads';   //2
        $destFolder = $storeFolder . "/";

        foreach($_SESSION[self::userAddPostImagesActions] as $action)
        {
            if($action->action != "add")
                continue;

            $targetFile = $destFolder . uniqid("post_image_");

            if (rename($action->data, $targetFile))
            {
                $uploadedFiles[] = $targetFile;
            }
            else
            {
                echo "Error occurred\n";
            }
        }

        $this->manager->savePostImages($postId, $uploadedFiles);
    }

    private function updateEditedPost()
    {
        if (!isset($_POST["id"]) || empty($_POST["id"]) ||
            !isset($_POST["title"]) || empty($_POST["title"]) ||
            !isset($_POST["content"]) || empty($_POST["content"]) ||
            !isset($_POST["published"]) || !($_POST["published"] === "true" || $_POST["published"] === "false")) {
            exit;
        }

        $postId = $_POST["id"];
        $postTitle = $_POST["title"];
        $postContent = $_POST["content"];
        $postPublished = $_POST["published"] === "true" ? true : false;

        $updated = $this->manager->updatePost($postId, $postTitle, $postContent, $postPublished);
        if ($updated) {
            header("HTTP/1.1 200 OK");
        } else {
            exit;
        }
    }
}