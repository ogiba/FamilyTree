<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 02:17
 */

namespace Database;

use Model\Post;
use Model\PostPage;

class PostsManager extends BaseDatabaseManager
{
    public function loadPosts($page = 0, $pageSize = 5) {
        $database = $this->createConnection();
        $selectedPage = $page * $pageSize;
        $stmt = $database->prepare("SELECT * FROM posts LIMIT $pageSize OFFSET $selectedPage");
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $results = array();

        $postPage = new PostPage();

        while ($stmt->fetch()){
            $results[] = array("id" => $data["id"]);
        }

        $postPage->setPosts($results);

        $stmt->reset();
        $stmt = $database->prepare("SELECT COUNT(*) FROM posts");

        $stmt->execute();
        $countData = $this->bindResult($stmt);
        if ($stmt->fetch()) {
            $totalNumberOfItems = $countData["COUNT(*)"];
            $postPage->setTotalItems($totalNumberOfItems);

            $totalNumberOfPages = ceil($totalNumberOfItems / $pageSize);
            $postPage->setNumberOfPages($totalNumberOfPages);
        }

        $postPage->setCurrentPage($page + 1);

        return $postPage;
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