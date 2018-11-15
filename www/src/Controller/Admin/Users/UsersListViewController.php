<?php
/**
 * Created by VSCode.
 * User: ogiba
 * Date: 08.11.2018
 * Time: 20:50
 */

namespace Controller\Admin\Users;

use Controller\Admin\BaseAdminViewController;
use Database\UserManager;
use Model\UserPage;

class UsersListViewController extends BaseAdminViewController
{

    /**
     * @var UserManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = new UserManager();
    }

    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        switch ($action) {
            case "getUsers":
                $this->usersPageAction();
                break;
            default:
                $this->viewIndex();
                break;
        }
    }

    private function viewIndex()
    {
        $orderByParam = isset($_GET["orderBy"]) ? $_GET["orderBy"] : "asc";
        $sortParam = isset($_GET["sortBy"]) ? $_GET["sortBy"] : "id";
        $pageSize = isset($_GET["pageSize"]) ? $_GET["pageSize"] : 1;

        $usersPage = $this->loadUsers($orderByParam, $sortParam, $pageSize);

        echo $this->render("/admin/users/users_view.html.twig", [
            "userLogged" => $this->userLogged,
            "usersPage" => $usersPage,
            "orderBy" => $orderByParam,
            "sortBy" => $sortParam
        ]);
    }

    public function usersPageAction()
    {
        $orderByParam = isset($_GET["orderBy"]) ? $_GET["orderBy"] : "asc";
        $sortParam = isset($_GET["sortBy"]) ? $_GET["sortBy"] : "id";
        $pageSize = isset($_GET["pageSize"]) ? $_GET["pageSize"] : 1;

        $usersPage = $this->loadUsers($orderByParam, $sortParam, $pageSize);

        echo $this->render("/admin/users/users_list.html.twig", array(
            "usersPage" => $usersPage,
            "orderBy" => $orderByParam,
            "sortBy" => $sortParam,
            "pageSize" => $pageSize
        ));
    }

    private function loadUsers(
        $orderByParam = "asc",
        $sortParam = "id",
        $limit = 10
    ) {
        $page = isset($_GET["page"]) ? $_GET["page"] : 0;

        $users = $this->manager->retriveUsers($page, $limit, $sortParam, $orderByParam);
        $totalNumberOfUsers = $this->manager->countUsers();

        $totalNumberOfPages = ceil($totalNumberOfUsers / $limit);

        $userPage = new UserPage();
        $userPage->setUsers($users);
        $userPage->setTotalItems($totalNumberOfUsers);
        $userPage->setNumberOfPages($totalNumberOfPages);
        $userPage->setCurrentPage($page + 1);

        return $userPage;
    }
}
