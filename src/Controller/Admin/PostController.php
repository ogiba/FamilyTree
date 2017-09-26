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
    protected function indexCustomAction($path)
    {
        if ($path == null) {
            $this->viewPostBehavior();
        } else if ($path == "edit") {
            $this->editPostBehavior();
        } else if ($path == "new") {
            $this->newPostBehavior();
        }
    }

    private function viewPostBehavior() {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            header("location: /not_found");
            exit;
        }

        $postId = $_GET["id"];

        $postManager = new PostsManager();
        $postToView = $postManager->loadPost($postId);

        echo $this->render("/admin/post/post_view.html.twig", [
            "userLogged" => $userLogged,
            "post" => $postToView
        ]);
    }

    private function editPostBehavior() {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            header("location: /not_found");
            exit;
        }

        echo $this->render("/admin/post/post_view.html.twig", [
            "userLogged" => $userLogged
        ]);
    }

    private function newPostBehavior() {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        echo $this->render("/admin/post/post_edit.html.twig");
    }

}