<?php

namespace AppBundle\Controller;

use AppBundle\Model\About;
use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        $aboutInfo = new About(" Quisque pharetra, urna mattis sed, posuere sit amet dui turpis dolor, porttitor
                                    odio.
                                    Nunc condimentum vitae, dapibus vitae, vestibulum ac, auctor mi. Curabitur eget
                                    imperdiet sagittis, nunc iaculis malesuada fames ac lectus. Ut sodales felis, in
                                    vehicula est. In hac habitasse platea dictumst. Proin orci. Integer egestas, dui
                                    dui,");

        $request->setLocale("pl");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            "posts" => $posts,
            "aboutInfo" => $aboutInfo
        ));
    }
}
