<?php

use API\PostController;
use API\TestController;
use Controller\Admin\InformationManageController;
use Controller\Admin\LoginController;
use Controller\Admin\PanelController;
use Controller\Admin\TreeBuildControler;
use Controller\IndexController;
use Controller\NotFoundController;
use Controller\PostViewController;
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

        $controller = $this->route($explodedPath);
        $name = isset($explodedPath[0]) ? $explodedPath[0] : null;
        $action = isset($explodedPath[1]) ? $explodedPath[1] : null;
        $params = [];

        if (count($explodedPath) > 2)
            $params = array_slice($explodedPath, 2);

        call_user_func_array([$controller, "action"], [$name, $action, $params]);
    }

    /**
     * @param string[] $path
     * @return \Controller\BaseController
     */
    private function route(array &$path)
    {
        // puste oznaczaja domyslny routing
        $routing = [
            "" => function () {
                return new IndexController();
            },
            "tree" => function () {
                return new TreeController();
            },
            "api" => [
                "post" => function () {
                    return new PostController();
                },
                "test" => function () {
                    return new TestController();
                }
            ],
            "admin" => [
                "" => function () {
                    return new PanelController();
                },
                "post" => function () {
                    return new \Controller\Admin\PostController();
                },
                "sections" => [
                    "" => function() { return new InformationManageController(); },
                    "section" => function() { return new InformationManageController(); }

                ],
                "tree_builder" => function (){
                    return new TreeBuildControler();
                },
                "login" => function () {
                    return new LoginController();
                },
                "logout" => function () {
                    return new LoginController();
                }
            ],
            "post" => function () {
                return new PostViewController();
            },
            "not_found" => function () {
                return new NotFoundController();
            }
        ];

        $currentRouting = $routing;

        while (count($path) > 0) {
            $pathItem = $path[0];

            if (isset($currentRouting[$pathItem])) {
                // jesli aktualny obiekt jest tablica pobierz go i powtorz petle
                if (is_array($currentRouting[$pathItem])) {
                    $currentRouting = $currentRouting[$pathItem];
                    array_shift($path);
                    continue;
                }

                // jesli nie jest tablica wywolaj funckje zapisana w $routing
                return $currentRouting[$pathItem]();
            }

            return new NotFoundController();
        }

        // domyslny routing
        if (isset($currentRouting[""]))
            return $currentRouting[""]();

        return new NotFoundController();
    }
}