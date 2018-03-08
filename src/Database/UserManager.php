<?php
/**
 * Created by PhpStorm.
 * User: robertogiba
 * Date: 08.03.2018
 * Time: 19:57
 */

namespace Database;


use Model\User;

class UserManager extends BaseDatabaseManager {

    /**
     * @return User[] array
     */
    public function retriveUsers()
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM users");
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $user = $this->arrayToObject($data, User::class);
            $result[] = $user;
        }

        $connection->close();
        return $result;
    }

    public function retriveUser($id)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $user = null;

        if ($stmt->fetch()) {
            $user = $this->arrayToObject($data, User::class);
        }

        $connection->close();
        return $user;
    }
}