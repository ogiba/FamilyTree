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
    const DEFAULT_PAGE_SIZE = 30;
    const DEFAULT_SORT_PARAM = "id";
    const DEFAULT_ORDER_DIR = "asc";
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
        $orderByParam = isset($_GET["orderBy"]) ? $_GET["orderBy"] : self::DEFAULT_ORDER_DIR;
        $sortParam = isset($_GET["sortBy"]) ? $_GET["sortBy"] : self::DEFAULT_SORT_PARAM;
        $pageSize = isset($_GET["pageSize"]) ? $_GET["pageSize"] : self::DEFAULT_PAGE_SIZE;

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
        $orderByParam = isset($_GET["orderBy"]) ? $_GET["orderBy"] : self::DEFAULT_ORDER_DIR;
        $sortParam = isset($_GET["sortBy"]) ? $_GET["sortBy"] : self::DEFAULT_SORT_PARAM;
        $pageSize = isset($_GET["pageSize"]) ? $_GET["pageSize"] : self::DEFAULT_PAGE_SIZE;

        $usersPage = $this->loadUsers($orderByParam, $sortParam, $pageSize);

        echo $this->render("/admin/users/users_list.html.twig", array(
            "usersPage" => $usersPage,
            "orderBy" => $orderByParam,
            "sortBy" => $sortParam,
            "pageSize" => $pageSize
        ));
    }

    private function loadUsers(
        $orderByParam = self::DEFAULT_ORDER_DIR,
        $sortParam = self::DEFAULT_SORT_PARAM,
        $limit = self::DEFAULT_PAGE_SIZE
    ) {
        $page = isset($_GET["page"]) ? $_GET["page"] : 0;

        if ($limit <= 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }

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
