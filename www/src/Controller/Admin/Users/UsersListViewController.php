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

class UsersListViewController extends BaseAdminViewController {

    /**
     * @var UserManager
     */
    private $manager;

    public function __construct() {
        $this->manager = new UserManager();
    }

    public function action($name, $action, $params)
    {
        parent::action($name, $action, $params);

        switch($action) {
            default: 
                $this->viewIndex();
                break;
        }
    }

    private function viewIndex()
    {
        $users = $this->loadMembers();

        echo $this->render("/admin/users/users_view.html.twig", [
            "userLogged" => $this->userLogged,
            "users" => $users
        ]);
    }

    private function loadMembers()
    {
        return $this->manager->retriveUsers();
    }
}