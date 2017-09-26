<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 18:52
 */

namespace Controller\Admin;

use Database\PostsManager;

class PanelController extends BaseAdminController
{
    protected function indexCustomAction()
    {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        $postPage = $this->loadPostsFromDb();

        echo $this->render("/admin/panel/panel.html.twig", [
            "userLogged" => $userLogged,
            "postsPage" => $postPage
        ]);
    }

    private function loadPostsFromDb() {
        $manager = new PostsManager();
        return $manager->loadPosts(0, 30);
    }
}