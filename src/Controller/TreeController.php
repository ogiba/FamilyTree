<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 02:48
 */

namespace Controller;


class TreeController extends BaseController
{
    public function indexAction(){
        echo $this->render("tree/tree.html.twig");
    }

    public function rebuildItem($request) {
        if (isset($request["data"]) && isset($request["id"])) {
            $params = array("data" => $request["data"], "id" => $request["id"]);

            echo $this->render("tree/tree_node.html.twig", $params);
        } else {
            echo "";
        }
    }
}