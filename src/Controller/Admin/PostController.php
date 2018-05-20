<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 26.09.2017
 * Time: 22:19
 */

namespace Controller\Admin;


use Database\PostsManager;
use Model\Response;
use Utils\StatusCode;

class PostController extends BaseAdminController {
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

        if (count($params) == 0) {
            $this->viewPostBehavior($action);
            return;
        }

        switch ($params[0]) {
            case null:
                $this->viewPostBehavior($action);
                break;
            case "edit":
                $this->editPostBehavior($action, $params);
                break;
            case "new":
                $this->newPostBehavior($params);
                break;
            case "remove":
                $this->deleteEditedPost($action);
                break;
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

    private function viewPostBehavior($postId)
    {
//        if (!isset($_GET["id"]) || empty($_GET["id"])) {
//            header("location: /not_found");
//            exit;
//        }

//        $postId = $_GET["id"];
        $postToView = $this->manager->loadPost($postId);

        echo $this->render("/admin/post/post_view.html.twig", [
            "userLogged" => $this->checkIfUserLogged(),
            "post" => $postToView
        ]);
    }

    private function editPostBehavior($postId, $pathArray)
    {
        if (count($pathArray) > 0 && $pathArray[0] == "update") {
            $this->updateEditedPost();
        } else {
//            if (!isset($_GET["id"]) || empty($_GET["id"])) {
//                header("location: /not_found");
//                exit;
//            }

//            $postId = $_GET["id"];

            $post = $this->manager->loadPost($postId);

            $saveAction = "updatePost($postId)";

            $_SESSION[self::userAddPostImagesActions] = [];

            echo $this->render("/admin/post/post_edit.html.twig", [
                "userLogged" => $this->checkIfUserLogged(),
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
            $saveAction = "savePost()";

            $_SESSION[self::userAddPostImagesActions] = [];

            echo $this->render("/admin/post/post_edit.html.twig", [
                "userLogged" => $this->checkIfUserLogged(),
                "action" => $saveAction
            ]);
        }
    }

    private function uploadFiles()
    {
        if (!empty($_FILES)) {
            $tempFile = $_FILES['file']['tmp_name'];
            $storeFolder = 'uploads/temp';   //2
            $destFolder = $storeFolder . "/";
            $targetFile = $destFolder . uniqid("post_image_");

            if (!file_exists($destFolder))
                mkdir($destFolder, 0x0777, true);

            if (move_uploaded_file($tempFile, $targetFile)) {
                $action = new \stdClass();
                $action->action = "add";
                $action->data = $targetFile;

                $_SESSION[self::userAddPostImagesActions][] = $action;
            } else {
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

        $uploadedFiles = $this->saveUploadedFiles();

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

            $uploadedFiles = $this->saveUploadedFiles();
            $this->manager->savePostImages($postId, $uploadedFiles);

            header("HTTP/1.1 200 OK");
        } else {
            exit;
        }
    }

    private function deleteEditedPost($postId)
    {
//        if (!isset($_POST["id"]) || empty($_POST["id"])) {
//            exit;
//        }

//        $postId = $_POST["id"];

        if (!is_numeric($postId)) {
            $responseCode = StatusCode::BAD_REQUEST;
            $response = new Response(StatusCode::getMessageForCode($responseCode), $responseCode);
            $this->sendJsonResponse($response);
            exit;
        }
        $postRemoved = $this->manager->removePost($postId);

        if ($postRemoved) {
//            header("Location: /admin");
            $response = new Response(StatusCode::getMessageForCode(StatusCode::OK),
                StatusCode::OK);
            $this->sendJsonResponse($response);
        } else {
            $response = new Response(StatusCode::getMessageForCode(StatusCode::UNPROCESSED_ENTITY),
                StatusCode::UNPROCESSED_ENTITY);
            $this->sendJsonResponse($response);
        }
        exit;
    }

    private function saveUploadedFiles()
    {
        $uploadedFiles = [];
        $storeFolder = 'uploads';   //2
        $destFolder = $storeFolder . "/";

        foreach ($_SESSION[self::userAddPostImagesActions] as $action) {
            if ($action->action != "add")
                continue;

            $targetFile = $destFolder . uniqid("post_image_");

            if (rename($action->data, $targetFile)) {
                $uploadedFiles[] = $targetFile;
            } else {
                echo "Error occurred\n";
            }
        }

        return $uploadedFiles;
    }
}