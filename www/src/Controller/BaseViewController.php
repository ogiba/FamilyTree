<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:12
 */

namespace Controller;

use Exception;
use Model\Response;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;
use Utils\Base\BaseController;
use Utils\ResponseHeaders;
use Utils\StatusCode;

abstract class BaseViewController extends BaseController {
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
            return $this->translate($key);
        }));
        return $twig->render($viewName, $properties);
    }

    /**
     * Looks for translation for given key and return it value
     *
     * @param string $key
     * @return string
     */
    protected function translate($key)
    {
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
            case "en":
                $jsonString = file_get_contents("Translations/translation-" . $lang . ".json", true);
                if ($jsonString) {
                    try {
                        $translatedString = json_decode($jsonString, true)[$key];
                    } catch (Exception $ex) {

                    }
                }
                break;
            default:
                //echo "PAGE EN - Setting Default";
                $jsonString = file_get_contents("Translations/translation-en.json", true);
                if ($jsonString) {
                    try {
                        $translatedString = json_decode($jsonString, true)[$key];
                    } catch (Exception $ex) {

                    }
                }
                break;
        }
        return $translatedString;
    }

    protected function checkIfUserLogged()
    {
        $userLogged = false;
        if (isset($_SESSION["token"])) {
            $userLogged = true;
        }

        return $userLogged;
    }

    /**
     * @param Response $response
     */
    protected function sendJsonResponse($response)
    {
        header(ResponseHeaders::CONTENT_TYPE_JSON);
        header("HTTP/1.1 " . StatusCode::getMessageForCode($response->getStatusCode()));
        echo json_encode($response);
    }
}