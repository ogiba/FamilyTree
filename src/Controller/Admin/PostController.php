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
    protected function indexCustomAction()
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

        $postManager = new PostsManager();
        $postToView = $postManager->loadPost($postId);

        echo $this->render("/admin/post/post_view.html.twig", [
            "userLogged" => $userLogged,
            "post" => $postToView
        ]);
    }

}