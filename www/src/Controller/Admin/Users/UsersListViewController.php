<?php
/**
 * Created by VSCode.
 * User: ogiba
 * Date: 08.11.2018
 * Time: 20:50
 */

namespace Controller\Admin\Users;

use Controller\Admin\BaseAdminViewController;

class UsersListViewController extends BaseAdminViewController {
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
        if (!isset($_SESSION["selectedFamily"])) {
            return;
        }

        echo $this->render("/admin/users/users_view.html.twig", [
            "userLogged" => $this->userLogged,
            // "family" => $selectedFamily,
            // "familyMembers" => $members
        ]);

        // $selectedFamily = $this->loadFamilyData($_SESSION["selectedFamily"]);
        // $members = $this->manager->loadFamily($selectedFamily->id);

        // echo $this->render("/admin/trees/tree_builder_members.html.twig", [
        //     "userLogged" => $this->userLogged,
        //     "family" => $selectedFamily,
        //     "familyMembers" => $members
        // ]);
    }
}