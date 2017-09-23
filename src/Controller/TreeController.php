<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 02:48
 */

namespace Controller;


class TreeController extends BaseController
{
    public function indexAction(){
        echo $this->render("tree/tree.html.twig");
    }
}