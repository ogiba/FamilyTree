<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 20.08.2017
 * Time: 21:18
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class TreeController
 * @package AppBundle\Controller
 *
 * @Route("/tree")
 */
class TreeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(){
        return $this->render(":tree:tree.html.twig");
    }
}