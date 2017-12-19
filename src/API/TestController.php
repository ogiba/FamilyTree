<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 02:59
 */

namespace API;


use Database\FamilyManager;
use Database\InformationManager;
use Database\PostsManager;

class TestController extends BaseRestController
{
    public function action($name, $action, $params)
    {
        //TODO: Allows to place test implementation of required methods and test it via restClient
        $posts = new FamilyManager();
        $result = $posts->getFamilyMemberDetails($_GET["id"]);
        echo json_encode($result);
    }
}