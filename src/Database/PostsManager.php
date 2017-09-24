<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 24.09.2017
 * Time: 02:17
 */

namespace Database;


class PostsManager extends BaseDatabaseManager
{
    public function loadPosts($page = 0, $pageSize = 1) {
        $database = $this->createConnection();
        $stmt = $database->prepare("SELECT * FROM posts LIMIT $page, $pageSize");
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $results = array();

        while ($stmt->fetch()){
            $results[] = array($data["id"]);
        }
    }
}