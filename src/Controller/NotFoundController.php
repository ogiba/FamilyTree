<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 15:58
 */

namespace Controller;


class NotFoundController extends BaseController
{
    public function notFoundAction() {
        header("HTTP/1.1 404 Not found");
        echo $this->render("not_found.html.twig");
    }
}