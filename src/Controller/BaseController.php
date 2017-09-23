<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:12
 */

namespace Controller;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

class BaseController
{
    /**
     * @param string $viewName
     * @param array $properties
     * @return string
     */
    public function render($viewName, $properties = []) {
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_Environment($loader, array());
        $twig->addFunction(new Twig_SimpleFunction("asset", function($path){
            return "web/assets/".$path;
        }));
        return $twig->render($viewName, $properties);
    }
}