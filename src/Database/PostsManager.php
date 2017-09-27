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
    /**
     * Load posts from db and return it as {@link PostPage} model
     *
     * @param int $page
     * @param int $pageSize
     * @return PostPage
     */
    public function loadPosts($page = 0, $pageSize = 5)
    {
        $database = $this->createConnection();
        $selectedPage = $page * $pageSize;
        $stmt = $database->prepare("SELECT p.id, p.title, p.content, p.published, p.timeStamp, p.shortDescription,
                                                u.nickName AS author 
                                            FROM posts p 
                                            INNER JOIN users AS u ON p.author = u.id 
                                            ORDER BY timeStamp 
                                            DESC LIMIT ? 
                                            OFFSET ?");
        $stmt->bind_param("ii", $pageSize, $selectedPage);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $results = array();

        $postPage = new PostPage();

        while ($stmt->fetch()) {
            $post = $this->arrayToObject($data, Post::class);
            $results[] = $post;
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
     * Allows to get post by his {@see $id} from database
     *
     * @param $id
     * @return Post mixed|null
     */
    public function loadPost($id)
    {
        $database = $this->createConnection();
        $stmt = $database->prepare("SELECT p.id, p.title, p.content, p.published, p.timeStamp, p.shortDescription, u.nickName AS author 
                                            FROM posts p INNER JOIN users AS u ON p.author = u.id 
                                            WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch()) {
            $result = $this->arrayToObject($data, Post::class);
            return $result;
        } else {
            header("HTTP/1.1 404 Not found");
            return null;
        }
    }

    public function savePost($title, $content)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $token = $_SESSION["token"];

        $database = $this->createConnection();
        $stmt = $database->prepare("INSERT INTO posts (title, content, author, shortDescription) SELECT ?,?, user,? FROM login_attempts WHERE token = ?");
        $shortDesc = substr($content, 0, 100);
        $stmt->bind_param("ssss", $title, $content, $shortDesc, $token);
        $stmt->execute();
        $stmt->fetch();
    }

    public function updatePost($id, $title, $content) {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $token = $_SESSION["token"];

        $database = $this->createConnection();
        $stmt = $database->prepare("UPDATE posts SET title = ?, content = ?, shortDescription = ? WHERE id = ?");
        $shortDesc = substr($content, 0, 100);
        $stmt->bind_param("sssi", $title, $content, $shortDesc, $id);
        $stmt->execute();
        $stmt->fetch();
    }
}