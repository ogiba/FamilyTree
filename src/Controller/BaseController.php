<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:12
 */

namespace Controller;

use Exception;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

abstract class BaseController
{
    const HEADER_CONTENT_TYPE_JSON = "Content-Type: application/json";
    /**
     * @param string|null $name
     * @param string|null $action
     * @param array $params
     */
    public abstract function action($name, $action, $params);

    /**
     * @param string $viewName
     * @param array $properties
     * @return string
     */
    protected function render($viewName, $properties = [])
    {
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_Environment($loader, array());
        $twig->addFunction(new Twig_SimpleFunction("asset", function ($path) {
            return "/web/assets/" . $path;
        }));
        $twig->addFunction(new Twig_SimpleFunction("trans", function ($key) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $translatedString = $key;
            switch ($lang) {
                case "pl":
                    //echo "PAGE EN";
                    $jsonString = file_get_contents("Translations/translation-pl.json", true);
                    if ($jsonString) {
                        try {
                            $translatedString = json_decode($jsonString, true)[$key];
                        } catch (Exception $ex) {

                        }
                    }
                    break;
                default:
                    //echo "PAGE EN - Setting Default";
                    $jsonString = file_get_contents("/src/Translations/translation-" . $lang . ".json", true);
                    if ($jsonString) {
                        try {
                            $translatedString = json_decode($jsonString, true)[$key];
                        } catch (Exception $ex) {

                        }
                    }
                    break;
            }
            return $translatedString;
        }));
        return $twig->render($viewName, $properties);
    }
}