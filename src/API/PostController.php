<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:06
 */

namespace API;

use Database\PostsManager;
use Model\PostPage;


/**
 * Class PostController
 * @package Api
 *
 */


class PostController extends BaseRestController
{
    const DEFAULT_PAGE_SIZE = 5;
    const STARTING_PAGE = 0;

    private $postsManager;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->setupSerializer();

        $this->postsManager = new PostsManager();
    }


    public function postListAction($request) {
        $pageSize = isset($request["pageSize"]) ? (int)$request["pageSize"] : 0;
        $pageNumber = isset($request["page"]) ?  (int)$request["page"] : 0;

        $postPageSize = $pageSize != null && $pageSize > 0 ? $pageSize : self::DEFAULT_PAGE_SIZE;

        $postsPage = $this->postsManager->loadPosts($pageNumber, $postPageSize);

        if (count($postsPage->getPosts()) > 0) {
            $jsonPage = $this->serializeManager->serializeJson($postsPage);

            header("Content-Type: application/json");
            header("HTTP/1.1 200 OK");
            echo $jsonPage;
        } else {
            header("HTTP/1.1 404 Not found");
            echo "";
        }
    }

    function getPostAction($request) {
        if (!isset($request["id"]) || empty($request["id"])){
            header("HTTP/1.1 422 Missing required parameter");
            echo "Required parameter not set";
            return;
        }

        $id = $request["id"];
        $selectedPost = $this->postsManager->loadPost($id);

        if (!is_null($selectedPost)){
            $jsonString = $this->serializeManager->serializeJson($selectedPost);
            header("Content-Type: application/json");
            header("HTTP/1.1 200 OK");
            echo $jsonString;
        } else {
            header("HTTP/1.1 404 Not found");
            echo "";
        }
    }
}