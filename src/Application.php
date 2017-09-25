<?php

use API\PostController;
use API\TestController;
use Controller\Admin\LoginController;
use Controller\Admin\PanelController;
use Controller\IndexController;
use Controller\NotFoundController;
use Controller\TreeController;

/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:23
 */

session_start();

class Application
{

    public function run()
    {
        $path = "";
        if (isset($_GET["path"])) {
            $path = $_GET["path"];
        }

        $explodedPath = explode("/", $path);

        if ($explodedPath[0] == "") {
            $index = new IndexController();
            if (isset($_GET["postPage"])) {
                $index->postPageAction($_GET["postPage"]);
            } else {
                $index->indexAction();
            }
        } else if ($explodedPath[0] == "tree") {
            $controller = new TreeController();

            if (count($explodedPath) > 1 && $explodedPath[1] == "rebuild") {
                $controller->rebuildItem($_REQUEST);
            } else {
                $controller->indexAction();
            }
        } else if ($explodedPath[0] == "api") {
            if ($explodedPath[1] == "post") {
                $postController = new PostController();

                if (isset($_REQUEST["id"])) {
                    $postController->getPostAction($_REQUEST);
                } else {
                    $postController->postListAction($_REQUEST);
                }
            } else if ($explodedPath[1] == "test") {
                $test = new TestController();
                $test->test($_REQUEST);
            }
        } else if ($explodedPath[0] == "admin") {
            if (count($explodedPath) == 1) {
                $panel = new PanelController();
                $panel->indexAction();
            } else if ($explodedPath[1] == "login") {
                $login = new LoginController();
                if (count($explodedPath) > 2) {
                    $login->loginAction($_POST);
                } else {
                    $login->indexAction();
                }
            } else if ($explodedPath[1] == "logout") {
                $login = new LoginController();
                $login->logoutAction();
            }
        } else {
            $notFound = new NotFoundController();
            $notFound->notFoundAction();
        }
    }
}