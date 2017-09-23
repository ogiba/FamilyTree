<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:25
 */

namespace Controller;


use Model\About;
use Model\Post;
use Model\PostPage;

class IndexController extends BaseController
{

    const NUMBER_OF_ITEMS = 5;
    const STARTING_PAGE = 1;

    public function indexAction() {

        $postsPage = $this->getPosts(self::NUMBER_OF_ITEMS, self::STARTING_PAGE);

        $aboutInfo = new About(" Quisque pharetra, urna mattis sed, posuere sit amet dui turpis dolor, porttitor
                                    odio.
                                    Nunc condimentum vitae, dapibus vitae, vestibulum ac, auctor mi. Curabitur eget
                                    imperdiet sagittis, nunc iaculis malesuada fames ac lectus. Ut sodales felis, in
                                    vehicula est. In hac habitasse platea dictumst. Proin orci. Integer egestas, dui
                                    dui,");

        echo $this->render("default/index.html.twig", array(
            "postsPage" => $postsPage,
            "aboutInfo" => $aboutInfo
        ));
    }

    public function postPageAction($page = self::STARTING_PAGE)
    {
        $postsPage = $this->getPosts(self::NUMBER_OF_ITEMS, $page);

        echo $this->render("default/posts.html.twig", array(
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