<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:06
 */

namespace AppBundle\Controller\Api;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Model\PostPage;


/**
 * Class PostController
 * @package AppBundle\Api
 *
 */

/**
 * @Route("/api")
 */
class PostController extends BaseRestController
{
    const DEFAULT_PAGE_SIZE = 10;
    const STARTING_PAGE = 1;
    /**
     * @Route("/posts")
     * @Method({"GET"})
     * @param Request $request
     * @return Response
     */
    public function postListAction(Request $request) {
        $this->setupSerializer();

        $postsPage = new PostPage();

        $pageSize = $request->query->get("pageSize");
        $pageNumber = $request->query->get("page");

        $postPageSize = $pageSize != null && $pageSize > 0 ? $pageSize : self::DEFAULT_PAGE_SIZE;

        $totalNumberOfItems = 10;
        $totalNumberOfPages = ceil($totalNumberOfItems / $postPageSize);

        $postsPage->setTotalItems($totalNumberOfItems);

        $selectedPageNumber = $pageNumber != null && $pageNumber > 0 && $pageNumber <= $totalNumberOfPages ?
            $pageNumber : self::STARTING_PAGE;

        $postsPage->setNumberOfPages($totalNumberOfPages);

        $postsPage->setCurrentPage($selectedPageNumber);

        $ps = array();
        for ($i = 0; $i < $postPageSize; $i++) {
            $ps[$i] = $i;
        }
        $postsPage->setPosts($ps);

        $jsonPage = $this->serializeManager->serializeJson($postsPage);

        $response = new Response($jsonPage);
        $response->headers->set("Content-Type", "application/json");

        return $response;
    }
}