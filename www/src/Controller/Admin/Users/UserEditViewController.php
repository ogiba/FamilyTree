<?php
/**
 * Created by VSCode.
 * User: ogiba
 * Date: 10.12.2018
 * Time: 21:25
 */

namespace Controller\Admin\Users;

use Controller\Admin\BaseAdminViewController;
use Database\UserManager;

class UserEditViewController extends BaseAdminViewController
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

        switch ($name) {
            default:
                $this->viewIndex($action);
                break;
        }
    }

    private function viewIndex($id)
    {
        echo $this->render("/admin/users/edit/user_edit.html.twig", [
            "userLogged" => $this->userLogged,
            "id" => $id
        ]);
    }
}
