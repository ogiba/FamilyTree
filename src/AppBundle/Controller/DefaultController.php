<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $posts = array();

        for ($j = 0; $j < 2; $j++) {
            $item = array();
            $item["numberOfPage"] = $j;
            $ps = array();
            for ($i = 0; $i < 5; $i++) {
                $ps[$i] = $i;
            }
            $item["posts"] = $ps;
            $posts[$j] = $item;
        }

        $request->setLocale("pl");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            "posts" => $posts
        ));
    }
}
