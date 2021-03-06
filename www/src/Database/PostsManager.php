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

class PostsManager extends BaseDatabaseManager {
    /**
     * Load posts from db and return it as {@link PostPage} model
     *
     * @param int $page
     * @param int $pageSize
     * @param bool $publishedOnly
     * @return PostPage
     */
    public function loadPosts($page = 0, $pageSize = 5, $publishedOnly = false)
    {
        $database = $this->createConnection();
        $selectedPage = $page * $pageSize;

        if (!$publishedOnly) {
            $stmt = $database->prepare("SELECT p.id, p.title, p.content, p.published, p.timeStamp, p.shortDescription,
                                                u.nickName AS author 
                                            FROM posts p 
                                            INNER JOIN users AS u ON p.author = u.id 
                                            ORDER BY timeStamp 
                                            DESC LIMIT ? 
                                            OFFSET ?");
        } else {
            $stmt = $database->prepare("SELECT p.id, p.title, p.content, p.published, p.timeStamp, p.shortDescription,
                                                u.nickName AS author 
                                            FROM posts p 
                                            INNER JOIN users AS u ON p.author = u.id 
                                            WHERE p.published = 1
                                            ORDER BY timeStamp 
                                            DESC LIMIT ? 
                                            OFFSET ?");
        }

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

        if (!$publishedOnly)
            $stmt = $database->prepare("SELECT COUNT(*) FROM posts");
        else
            $stmt = $database->prepare("SELECT COUNT(*) FROM posts WHERE published = 1");

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
        $stmt = $database->prepare("SELECT 
                                                p.id, 
                                                p.title,
                                                p.content,
                                                p.published,
                                                p.timeStamp,
                                                p.shortDescription,
                                                u.nickName AS author,
                                                u1.nickName AS modifiedBy
                                            FROM posts p 
                                            INNER JOIN users AS u ON p.author = u.id
                                            LEFT JOIN users AS u1 ON p.modifiedBy = u1.id
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

    public function savePost($title, $content, $published)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $token = $_SESSION["token"];

        $published = $published ? 1 : 0;
        $database = $this->createConnection();
        $stmt = $database->prepare("INSERT INTO posts (title, content, published, author, shortDescription) SELECT ?,?,?, user, ? FROM login_attempts WHERE token = ?");
        $shortDesc = substr($content, 0, 100);
        $stmt->bind_param("ssiss", $title, $content, $published, $shortDesc, $token);
        $stmt->execute();
        $stmt->fetch();

        return $stmt->insert_id;
    }

    public function savePostImages($postId, $files)
    {
        if (!isset($_SESSION["token"])) {
            exit;
        }

        $token = $_SESSION["token"];

        foreach ($files as $file) {
            $database = $this->createConnection();
            $stmt = $database->prepare("INSERT INTO post_images (image, postId) VALUES(?, ?)");
            $stmt->bind_param("si", $file, $postId);
            $stmt->execute();
            $stmt->fetch();
        }
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $content
     * @param bool $published
     * @return bool
     */
    public function updatePost($id, $title, $content, $published)
    {
        if (!isset($_SESSION["token"])) {
            return false;
        }

        $token = $_SESSION["token"];
        $published = $published ? 1 : 0;

        $database = $this->createConnection();
        $stmt = $database->prepare("UPDATE posts SET title = ?, content = ?, published = ?, shortDescription = ?,
                                        modifiedBy = (SELECT user FROM login_attempts WHERE token = ?) WHERE id = ?");
        $shortDesc = substr($content, 0, 100);
        $stmt->bind_param("ssissi", $title, $content, $published, $shortDesc, $token, $id);
        $stmt->execute();
        $stmt->fetch();

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removePost($id)
    {
        $isSucceed = false;

        if (!isset($_SESSION["token"])) {
            return $isSucceed;
        }

        $database = $this->createConnection();
        $stmt = $database->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $isSucceed = $stmt->affected_rows > 0;

        $database->close();
        return $isSucceed;
    }
}