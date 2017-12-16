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
    public function action($name, $action, $params)
    {
        if ($action == "rebuild") {
            $this->rebuildItem($_REQUEST);
        } elseif ($action == "load_tree") {
            $this->loadTree($_REQUEST);
        } else {
            $this->indexAction();
        }
    }

    public function indexAction(){
        echo $this->render("tree/tree.html.twig");
    }

    public function rebuildItem($request) {
        if (isset($request["data"]) && isset($request["id"]) && isset($request["position"])) {
            $position = json_decode($request["position"]);
            $params = array("data" => $request["data"], "id" => $request["id"]);
            $response = array("position" => $position, "item" => $this->render("tree/tree_node.html.twig", $params));

            echo json_encode($response);
        } else {
            echo "";
        }
    }

    public function loadTree($request) {
        $file = file_get_contents("./data/trees/tree.json");
        $jsonFile = json_decode($file, true);

        echo json_encode($jsonFile);
    }
}