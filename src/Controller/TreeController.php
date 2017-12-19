<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 02:48
 */

namespace Controller;


use Database\FamilyManager;

class TreeController extends BaseController
{
    const MODE_DEBUG = "debug";

    public function action($name, $action, $params)
    {
        if ($action == "rebuild") {
            $this->rebuildItem($_REQUEST);
        } elseif ($action == "load_tree") {
//            $this->loadTree($_REQUEST);
            $this->testLoadTree($_REQUEST);
        } else {
            $this->indexAction($_REQUEST);
        }
    }

    public function indexAction($request)
    {
        $debugMode = false;
        if (isset($request["mode"])) {
            $debugMode = $request["mode"] == $this::MODE_DEBUG ? true : false;
        }

        $params = array("debug" => $debugMode);
        echo $this->render("tree/tree.html.twig", $params);
    }

    public function rebuildItem($request)
    {
        $position = null;

        if (isset($request["position"])) {
            $position = $request["position"];
        }

        if (isset($_POST["data"]) && isset($request["id"])) {
            $personData = $_POST["data"];
            $params = array("person" => $personData, "id" => $request["id"]);
            $response = array("position" => $position, "item" => $this->render("tree/tree_node.html.twig", $params));

            header($this::HEADER_CONTENT_TYPE_JSON);
            echo json_encode($response);
        } else {
            echo "";
        }
    }

    public function loadTree($request)
    {
        $file = file_get_contents("./data/trees/tree.json");
        $jsonFile = json_decode($file, true);

        header($this::HEADER_CONTENT_TYPE_JSON);
        echo json_encode($jsonFile);
    }

    private function testLoadTree($request)
    {
        if (!isset($request["family"])) {
            return;
        }

        $familyManager = new FamilyManager();
        $result = $familyManager->loadFamilyMembers($request["family"]);
        header($this::HEADER_CONTENT_TYPE_JSON);
        echo json_encode($result);
    }
}