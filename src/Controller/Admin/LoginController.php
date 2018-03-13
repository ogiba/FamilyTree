<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 19:00
 */

namespace Controller\Admin;


use Controller\BaseController;
use Database\LoginManager;
use Model\NewResponse;
use Utils\SerializeManager;
use Utils\StatusCode;

class LoginController extends BaseController {
    public function action($name, $action, $params)
    {
        if ($name == "login") {
            if (count($_POST) > 0)
                $this->loginAction($_POST);
            else
                $this->indexAction();
        } else if ($name == "logout") {
            $this->logoutAction();
        }
    }

    public function indexAction()
    {
        echo $this->render("admin/login.html.twig");
    }

    public function loginAction($data)
    {
        $serializer = new SerializeManager();
        if (!isset($data["username"]) || empty($data["username"])) {
            $response = new NewResponse("Required username", StatusCode::UNPROCESSED_ENTITY);
            $this->sendJsonResponse($response);
            exit;
        }

        if (!isset($data["password"]) || empty($data["password"])) {
            $response = new NewResponse("Required password", StatusCode::UNPROCESSED_ENTITY);
            $this->sendJsonResponse($response);
            exit;
        }

        $username = $data["username"];
        $pw = $data["password"];

        $manager = new LoginManager();
        $userLogged = $manager->loginUser($username, $pw);

        if (is_null($userLogged)) {
            $response = new NewResponse($this->translate("admin-login-failed"), StatusCode::UNATHORIZED);
            $this->sendJsonResponse($response);
            exit;
        }

        $previousLocation = "/";
        if (isset($_SESSION["url"])) {
            $previousLocation = $_SESSION["url"];
        }

        $_SESSION["token"] = $userLogged;

        header("location: $previousLocation");
        exit;
    }

    public function logoutAction()
    {
        $manager = new LoginManager();
        $serializer = new SerializeManager();
        $isLoggedOut = $manager->logoutUser();

        if ($isLoggedOut) {
            $response = new NewResponse("User logged out", StatusCode::OK);
            $this->sendJsonResponse($response);
            header("Location: /");
        }
    }

    /**
     * @param NewResponse $response
     */
    private function sendJsonResponse($response)
    {
        header("HTTP/1.1 " . StatusCode::getMessageForCode($response->getStatusCode()));
        header("Content-Type: application/json");
        echo json_encode($response);
    }
}