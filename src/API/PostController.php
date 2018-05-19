<?php
/**
 * User: ogiba
 * Date: 19.08.2017
 * Time: 22:06
 */

namespace API;

use Database\PostsManager;
use Model\PostPage;
use Model\Response;
use Utils\StatusCode;


/**
 * Class PostController
 * @package Api
 *
 */
class PostController extends BaseRestController {
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

    function action($name, $action, $params)
    {
        switch ($action) {
            case "":
                if (!isset($_GET["id"])) {
                    $this->postListAction($_REQUEST);
                } else {
                    $this->getPostAction($_REQUEST);
                }
                break;
            case "remove":
                $this->handleRemove();
                break;
            default:
                break;
        }
    }

    public function postListAction($request)
    {
        $pageSize = isset($request["pageSize"]) ? (int)$request["pageSize"] : 0;
        $pageNumber = isset($request["page"]) ? (int)$request["page"] : 0;

        $postPageSize = $pageSize != null && $pageSize > 0 ? $pageSize : self::DEFAULT_PAGE_SIZE;

        $postsPage = $this->postsManager->loadPosts($pageNumber, $postPageSize);

        if (count($postsPage->getPosts()) > 0) {
            $this->sendJsonResponse($postsPage);
        } else {
            $this->sendJsonResponse(null, StatusCode::NOT_FOUND);
        }
    }

    function getPostAction($request)
    {
        if (!isset($request["id"]) || empty($request["id"])) {
            header("HTTP/1.1 422 Missing required parameter");
            echo "Required parameter not set";
            return;
        }

        $id = $request["id"];
        $selectedPost = $this->postsManager->loadPost($id);

        if (!is_null($selectedPost)) {
            $this->sendJsonResponse($selectedPost);
        } else {
            $this->sendJsonResponse(null, StatusCode::NOT_FOUND);
        }
    }

    private function handleRemove()
    {
        $requestHeaders = getallheaders();
        if (!isset($requestHeaders["Authorization"])) {
            $this->sendJsonResponse(null, StatusCode::UNATHORIZED);
            exit;
        }

        $token = $this->getBearerToken($requestHeaders["Authorization"]);

        if (!isset($_GET["id"])) {
            $this->sendJsonResponse(null, StatusCode::BAD_REQUEST);
            exit;
        } else if (is_null($token)) {
            $this->sendJsonResponse(null, StatusCode::UNATHORIZED);
            exit;
        }

        $isRemoved = $this->postsManager->removePost($_GET["id"]);

        if ($isRemoved) {
            $this->sendJsonResponse(null, StatusCode::OK);
        } else {
            $this->sendJsonResponse(null, StatusCode::UNPROCESSED_ENTITY);
        }
    }

    private function getBearerToken($headers)
    {
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}