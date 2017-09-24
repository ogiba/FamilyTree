<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 23.09.2017
 * Time: 00:25
 */

namespace Controller;


use Database\InformationManager;
use Database\PostsManager;
use Model\About;
use Model\PostPage;

class IndexController extends BaseController
{

    const NUMBER_OF_ITEMS = 5;
    const STARTING_PAGE = 0;

    public function indexAction() {

        $postsPage = $this->getPosts(self::NUMBER_OF_ITEMS, self::STARTING_PAGE);
        $aboutInfo = $this->getAbout();

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

    /**
     * @param $pageSize
     * @param $pageNumber
     * @return PostPage
     */
    private function getPosts($pageSize, $pageNumber)
    {
        $postsManager = new PostsManager();
        $postsPage = $postsManager->loadPosts($pageNumber, $pageSize);

        return $postsPage;
    }

    /**
     * @return About|null
     */
    private function getAbout() {
        $informationManager = new InformationManager();
        return $informationManager->loadAboutMe();
    }
}