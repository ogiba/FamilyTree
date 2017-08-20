<?php

namespace AppBundle\Controller;

use AppBundle\Model\About;
use AppBundle\Model\Post;
use AppBundle\Model\PostPage;
use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    const NUMBER_OF_ITEMS = 5;
    const STARTING_PAGE = 1;

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        $postsPage = $this->getPosts(self::NUMBER_OF_ITEMS, self::STARTING_PAGE);

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

    /**
     * @Route("/page/{page}")
     * @Method({"GET"})
     * @param $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postPageAction($page)
    {

        $postsPage = $this->getPosts(self::NUMBER_OF_ITEMS, $page);

        return $this->render(":default:posts.html.twig", array(
            "postsPage" => $postsPage
        ));
    }

    private function getPosts($pageSize, $pageNumber)
    {
        $postsPage = new PostPage();

        $postPageSize = $pageSize != null && $pageSize > 0 ? $pageSize : self::NUMBER_OF_ITEMS;

        $totalNumberOfItems = 20;
        $totalNumberOfPages = ceil($totalNumberOfItems / $postPageSize);

        $postsPage->setTotalItems($totalNumberOfItems);

        $selectedPageNumber = $pageNumber != null && $pageNumber > 0 && $pageNumber <= $totalNumberOfPages ?
            $pageNumber : self::STARTING_PAGE;

        $postsPage->setNumberOfPages($totalNumberOfPages);

        $postsPage->setCurrentPage($selectedPageNumber);

        $ps = array();
        for ($i = 0; $i < $postPageSize; $i++) {
            $post = new Post("Test $i $pageNumber", "Quisque pharetra, urna mattis sed, posuere sit amet dui turpis dolor, porttitor
                odio.
                Nunc condimentum vitae, dapibus vitae, vestibulum ac, auctor mi. Curabitur eget
                imperdiet sagittis, nunc iaculis malesuada fames ac lectus. Ut sodales felis, in
                vehicula est. In hac habitasse platea dictumst. Proin orci. Integer egestas, dui
                dui,
                porta tincidunt. Pellentesque fermentum in, vulputate tempor diam. Aenean lacus
                tellus
                non nulla. Aenean et magnis dis parturient montes, nascetur ridiculus mus. Vivamus
                consequat mollis ut, gravida sit amet tellus malesuada velit vitae magna non erat
                volutpat. Curabitur vel laoreet urna. Sed eu nulla. Sed laoreet urna. Aenean quis
                lectus
                eu libero. Pellentesque bibendum, urna et netus et malesuada arcu luctus
                scelerisque.
                Maecenas feugiat sagittis luctus sagittis. Curabitur sed ante. In nunc volutpat
                facilisis, wisi vel laoreet condimentum, pulvinar mollis. Nulla diam vel bibendum
                sem,
                posuere cubilia Curae, In mollis, metus. Maecenas diam mauris, consequat non,
                lobortis
                quis, tincidunt eget, ultricies nulla, accumsan augue justo ipsum cursus a,
                convallis
                ac, tincidunt quis, massa. Vestibulum fermentum. Vivamus ullamcorper.", "20.08.2017");
            $ps[$i] = $post;
        }
        $postsPage->setPosts($ps);

        return $postsPage;
    }
}
