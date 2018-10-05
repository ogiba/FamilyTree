<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 08.03.2018
 * Time: 20:05
 */

namespace API;


use Database\UserManager;

class UserController extends BaseRestController {

    /**
     * @var UserManager
     */
    private $manager;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->manager = new UserManager();
    }


    public function action($name, $action, $params)
    {
        //TODO: Allows to place test implementation of required methods and test it via restClient

        switch ($action) {
            case "":
                $users = $this->manager->retriveUsers();
                $this->sendJsonResponse($users);
                break;
            case "details":
                $user = $this->manager->retriveUser($params[0]);
                $this->sendJsonResponse($user);
                break;
            default:
                break;
        }
    }
}