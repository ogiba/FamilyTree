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
            default:
                $this->viewIndex();
                break;
        }
    }

    private function viewIndex()
    {
        $usersPage = $this->loadMembers();

        echo $this->render("/admin/users/users_view.html.twig", [
            "userLogged" => $this->userLogged,
            "usersPage" => $usersPage
        ]);
    }

    private function loadMembers()
    {
        $page = isset($_GET["page"]) ? $_GET["page"] : 0;
        
        $pageSize = isset($_GET["pageSize"]) ? $_GET["pageSize"] : 30;

        $users = $this->manager->retriveUsers($page, $pageSize);
        $totalNumberOfUsers = $this->manager->countUsers();

        $totalNumberOfPages = ceil($totalNumberOfUsers / $pageSize);

        $userPage = new UserPage();
        $userPage->setUsers($users);
        $userPage->setTotalItems($totalNumberOfUsers);
        $userPage->setNumberOfPages($totalNumberOfPages);
        $userPage->setCurrentPage($page + 1);

        return $userPage;
    }
}
