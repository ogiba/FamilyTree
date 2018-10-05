<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 02:59
 */

namespace API;


use Database\FamilyManager;
use Model\Response;
use Utils\StatusCode;

class TestController extends BaseRestController {
    public function action($name, $action, $params)
    {
        //TODO: Allows to place test implementation of required methods and test it via restClient
        $posts = new FamilyManager();
        if ($action == "") {
            $result = $posts->loadFamilyMembers($_GET["id"]);
            echo json_encode($result);
        } else if ($action == "test1") {
            $result = $posts->getFamilyMemberDetails($_GET["id"]);
            echo json_encode($result);
        } else if ($action == "getSelectedTree") {
            $result = $posts->loadFamilyMembersForMember($_GET["familyId"],$_GET["id"]);

            $response = null;

            if (is_null($result)) {
                $response = new Response("", StatusCode::NOT_FOUND);
            } else {
                $response = new Response("", StatusCode::OK);
                $response->setContent($result);
            }
            $this->sendJsonNewResponse($response);
        }
    }
}