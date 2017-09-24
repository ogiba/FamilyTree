<?php
use API\PostController;
use API\TestController;
use Controller\IndexController;
use Controller\NotFoundController;
use Controller\TreeController;

/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:23
 */
class Application
{
    public function run(){
        $path = "";
        if (isset($_GET["path"])) {
            $path = $_GET["path"];
        }

        $explodedPath = explode("/", $path);

        if ($explodedPath[0] == "") {
                $index = new IndexController();
            if (isset($_GET["postPage"])){
                $index->postPageAction($_GET["postPage"]);
            } else {
                $index->indexAction();
            }
        } else if ($explodedPath[0] == "tree") {
            $controller = new TreeController();
            $controller->indexAction();
        } else if ($explodedPath[0] == "api") {
            if ($explodedPath[1] == "post") {
                $postController = new PostController();

                $postController->postListAction($_REQUEST);
            } else if($explodedPath[1] == "test") {
                $test = new TestController();
                $test->test($_REQUEST);
            }
        } else {
            $notFound = new NotFoundController();
            $notFound->notFoundAction();
        }
    }
}