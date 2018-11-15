<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 08.03.2018
 * Time: 20:05
 */

namespace API;

use Database\UserManager;
use Model\UserPage;

class UserController extends BaseRestController
{

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
                $pageSize = 5;

                $users = $this->manager->retriveUsers();
                $totalNumberOfUsers = $this->manager->countUsers();
        
                $totalNumberOfPages = ceil($totalNumberOfUsers / $pageSize);
        
                $userPage = new UserPage();
                $userPage->setUsers($users);
                $userPage->setTotalItems($totalNumberOfUsers);
                $userPage->setNumberOfPages($totalNumberOfPages);
                $userPage->setCurrentPage($page + 1);

                $this->sendJsonResponse($userPage);
                break;
            case "details":
                $user = $this->manager->retriveUser($params[0]);
                $this->sendJsonResponse($user);
                break;
            case "count":
                $this->sendJsonResponse($numberOfItems);
                break;
            default:
                break;
        }
    }
}
