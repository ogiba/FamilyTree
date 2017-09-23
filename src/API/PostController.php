<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:06
 */

namespace API;

use Model\PostPage;


/**
 * Class PostController
 * @package Api
 *
 */


class PostController extends BaseRestController
{
    const DEFAULT_PAGE_SIZE = 10;
    const STARTING_PAGE = 1;


    public function postListAction($request) {
        $this->setupSerializer();

        $postsPage = new PostPage();

        $pageSize = isset($request["pageSize"]) ? (int)$request["pageSize"] : 0;
        $pageNumber = isset($request["page"]) ?  (int)$request["page"] : 0;

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

        header('Content-Type: application/json');
        echo $jsonPage;
    }
}