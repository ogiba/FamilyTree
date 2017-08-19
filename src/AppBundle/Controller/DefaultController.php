<?php

namespace AppBundle\Controller;

use AppBundle\Model\About;
use AppBundle\Model\PostPage;
use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        $postsPage = new PostPage();

        $totalNumberOfItems = 10;
        $itemsForPage = 5;

        $postsPage->setTotalItems($totalNumberOfItems);
        $totalNumberOfPages = $totalNumberOfItems / $itemsForPage;
        $postsPage->setNumberOfPages($totalNumberOfPages);

        $postsPage->setCurrentPage(1);

        $ps = array();
        for ($i = 0; $i < $itemsForPage; $i++) {
            $ps[$i] = $i;
        }
        $postsPage->setPosts($ps);

        $aboutInfo = new About(" Quisque pharetra, urna mattis sed, posuere sit amet dui turpis dolor, porttitor
                                    odio.
                                    Nunc condimentum vitae, dapibus vitae, vestibulum ac, auctor mi. Curabitur eget
                                    imperdiet sagittis, nunc iaculis malesuada fames ac lectus. Ut sodales felis, in
                                    vehicula est. In hac habitasse platea dictumst. Proin orci. Integer egestas, dui
                                    dui,");

        $request->setLocale("pl");
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            "postsPage" => $postsPage,
            "aboutInfo" => $aboutInfo
        ));
    }
}
