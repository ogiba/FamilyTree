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
     * @param $id
     * @return Post mixed|null
     */
    public function loadPost($id) {
        $database = $this->createConnection();
        $stmt = $database->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch()){
            $result = $this->arrayToObject($data, Post::class);
            return $result;
        } else {
            header("HTTP/1.1 404 Not found");
            return null;
        }
    }
}