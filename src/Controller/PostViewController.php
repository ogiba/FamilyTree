<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 28.09.2017
 * Time: 20:12
 */

namespace Controller;


use Database\PostsManager;

class PostViewController extends BaseController
{
    private $manager;

    /**
     * PostViewController constructor.
     */
    public function __construct()
    {
        $this->manager = new PostsManager();
    }

    public function action($name, $action, $params)
    {
        if (!isset($_GET["id"]) || empty($_GET["id"]))
        {
            header("location: /not_found");
            exit;
        }

        $postID = $_GET["id"];

        $selectedPost = $this->getSelectedPost($postID);

        echo $this->render("/post/post_view.html.twig", [
            "post" => $selectedPost
        ]);
    }

    private function getSelectedPost($id) {
        return $this->manager->loadPost($id);
    }
}