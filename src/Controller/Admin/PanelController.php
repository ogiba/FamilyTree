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
    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        $postPage = $this->loadPostsFromDb();

        echo $this->render("/admin/panel/panel.html.twig", [
            "userLogged" => $this->checkIfUserLogged(),
            "postsPage" => $postPage
        ]);
    }

    private function loadPostsFromDb() {
        $manager = new PostsManager();
        return $manager->loadPosts(0, 30);
    }
}