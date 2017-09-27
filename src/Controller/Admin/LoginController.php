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
use Model\Response;
use Utils\SerializeManager;

class LoginController extends BaseController
{
    public function indexAction()
    {
        echo $this->render("admin/login.html.twig");
    }

    public function loginAction($data)
    {
        $serializer = new SerializeManager();
        if (!isset($data["username"])) {
            $response = new Response("Required username", 422);
            $jsonResponse = $serializer->serializeJson($response);
            header("HTTP/1.1 422 Missed required parameter");
            echo $jsonResponse;
            exit;
        }

        if (!isset($data["password"])) {
            $response = new Response("Required password", 422);
            $jsonResponse = $serializer->serializeJson($response);
            header("HTTP/1.1 422 Missed required parameter");
            echo $jsonResponse;
            exit;
        }

        $username = $data["username"];
        $pw = $data["password"];

        $manager = new LoginManager();
        $userLogged = $manager->loginUser($username, $pw);

        if (is_null($userLogged)) {
            $response = new Response("Login user failed", 422);
            $jsonResponse = $serializer->serializeJson($response);
            echo $jsonResponse;
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $previousLocation = "/";
        if (isset($_SESSION["url"])) {
            $previousLocation = $_SESSION["url"];
        }

        $_SESSION["token"] = $userLogged;
//        header("HTTP/1.1 200 OK");

        header("location: $previousLocation");
//        $response = new Response("Login successful", 200);
//        $serializer = new SerializeManager();
//        $json = $serializer->serializeJson($response);
//        echo $json;
        exit;
    }

    public function logoutAction()
    {
        $manager = new LoginManager();
        $serializer = new SerializeManager();
        $isLoggedOut = $manager->logoutUser();

        if ($isLoggedOut) {
            $response = new Response("User logged out", 200);
            header("HTTP/1.1 200 OK");
            header("Content-Type: application/json");
            echo $serializer->serializeJson($response);
            header("Location: /");
        }
    }
}