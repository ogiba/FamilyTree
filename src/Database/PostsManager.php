<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 02:17
 */

namespace Database;


use HttpRequest;
use Model\Post;

class PostsManager extends BaseDatabaseManager
{
    public function loadPosts($page = 0, $pageSize = 5) {
        $database = $this->createConnection();
        $stmt = $database->prepare("SELECT * FROM posts LIMIT $page, $pageSize");
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $results = array();

        while ($stmt->fetch()){
            $results[] = array("id" => $data["id"]);
        }

        echo json_encode($results);
    }

    /**
     * Allows to get post by his ID from database
     *
     * @param $request
     */
    public function loadPost($request) {
        if (!isset($request["id"]) || empty($request["id"])){
            header("HTTP/1.1 422 Missing required parameter");
            echo "Required parameter not set";
            return;
        }

        $id = $request["id"];

        $database = $this->createConnection();
        $stmt = $database->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch()){
            $result = $this->arrayToObject($data, Post::class);
            echo json_encode($result);
        } else {
            header("HTTP/1.1 404 Not found");
            echo "";
        }
    }
}