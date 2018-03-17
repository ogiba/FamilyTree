<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 02:48
 */

namespace Controller;


use Database\FamilyManager;
use Model\FamilyMember;
use Model\NewResponse;
use Model\TreeResponse;
use Utils\ResponseHeaders;
use Utils\StatusCode;

class TreeController extends BaseController {
    const MODE_DEBUG = "debug";

    public function action($name, $action, $params)
    {
        if ($action == "rebuild") {
            $this->rebuildItem($_REQUEST);
        } elseif ($action == "load_tree") {
            $this->loadTree($_REQUEST);
        } elseif ($action == "getDetails") {
            $this->retriveMemberDetails($_REQUEST);
        } else {
            $this->indexAction($_REQUEST);
        }
    }

    public function indexAction($request)
    {
        $debugMode = false;
        if (isset($request["mode"]) && $this->checkIfUserLogged()) {
            $debugMode = $request["mode"] == $this::MODE_DEBUG ? true : false;
        }

        $params = array("debug" => $debugMode, "userLogged" => $this->checkIfUserLogged());
        echo $this->render("tree/tree.html.twig", $params);
    }

    public function rebuildItem($request)
    {
        $position = null;

        if (isset($request["position"])) {
            $position = $request["position"];
        }

        $response = null;
        if (isset($_POST["data"]) && isset($request["id"])) {
            $personData = $_POST["data"];
            $params = array("person" => $personData, "id" => $request["id"]);
            $responseContent = array("position" => $position, "item" => $this->render("tree/tree_node.html.twig", $params));

            $response = new NewResponse("", StatusCode::OK);
            $response->setContent($responseContent);
        } else {
            $response = new NewResponse("", StatusCode::UNPROCESSED_ENTITY);
        }

        $this->sendJsonNewResponse($response);
    }

    public function testLoadTree($request)
    {
        $file = file_get_contents("./data/trees/tree.json");
        $jsonFile = json_decode($file, true);

        header(ResponseHeaders::CONTENT_TYPE_JSON);
        echo json_encode($jsonFile);
    }

    private function loadTree($request)
    {
        if (!isset($request["family"])) {
            $response = new NewResponse("", StatusCode::UNPROCESSED_ENTITY);
            $this->sendJsonNewResponse($response);
            return;
        }

        $familyManager = new FamilyManager();
        $result = $familyManager->loadFamilyMembers($request["family"]);

        $template = $this->readNodeTemplate("tree_node_member.html");
        $pairTemplate = $this->readNodeTemplate("tree_node_family.html");

        $responseContent = new TreeResponse($result, $template, $pairTemplate);

        $response = new NewResponse("", StatusCode::OK);
        $response->setContent($responseContent);
        $this->sendJsonNewResponse($response);
    }

    private function retriveMemberDetails($request)
    {
        if (!isset($request["id"])) {
            echo "Required parameter not set";
            return;
        }

        $memberId = $request["id"];
        $familyManager = new FamilyManager();

        $selectedMember = $familyManager->getFamilyMemberDetails($memberId);
        $selectedMember->firstParent = $this->loadParentDataForId($familyManager, $selectedMember->firstParent);
        $selectedMember->secondParent = $this->loadParentDataForId($familyManager, $selectedMember->secondParent);

        $memberPartner = $selectedMember->partner;
        if (!is_null($memberPartner) && $memberPartner instanceof FamilyMember) {
            $memberPartner->firstParent = $this->loadParentDataForId($familyManager, $memberPartner->firstParent);
            $memberPartner->secondParent = $this->loadParentDataForId($familyManager, $memberPartner->secondParent);
        }

        $params = array("member" => $selectedMember);
        echo $this->render("tree/templates/tree_member_details.html.twig", $params);

    }

    /**
     * @param int|null $parentId
     * @param FamilyManager $familyManager
     * @return FamilyMember | null
     */
    private function loadParentDataForId($familyManager, $parentId)
    {
        if (is_null($parentId)) {
            return null;
        }

        return $familyManager->getFamilyMemberDetails($parentId);
    }

    /**
     * Get content of file that is stored at server with given templateName
     *
     * @param $templateName
     * @return bool|string
     */
    private function readNodeTemplate($templateName)
    {
        return file_get_contents("views/tree/templates/" . $templateName);
    }
}