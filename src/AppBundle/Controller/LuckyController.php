<?php
/**
 * User: ogiba
 * Date: 07.08.2017
 * Time: 01:31
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends Controller {
    /**
     * @Route("/lucky/number")
     */
    public function numberAction(){
        $number = mt_rand(0, 100);

//        return new Response('<html><body><div>'.$number.'</div></body></html>');
        return $this->render("lucky/number.html.twig",array(
            "number" => $number
        ));
    }

    /**
     * @Route("/lucky/number/{max}")
     */
    public function maxNumberAction($max){
        $number = mt_rand(0, $max);

        return $this->render("lucky/number.html.twig",array(
            "number" => $number
        ));
    }
}
