<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 02:59
 */

namespace API;


use Database\FamilyManager;

class TestController extends BaseRestController
{
    public function action($name, $action, $params)
    {
        //TODO: Allows to place test implementation of required methods and test it via restClient
        if ($action == "") {
            $posts = new FamilyManager();
            $result = $posts->loadFamilyMembers($_GET["id"]);
            echo json_encode($result);
        } else if ("test1") {
            $posts = new FamilyManager();
            $result = $posts->getFamilyMemberDetails($_GET["id"]);
            echo json_encode($result);
        }
    }
}