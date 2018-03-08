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

    public function retriveUsers()
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM users");
        $stmt->execute();

        $data = $this->bindResult($stmt);
        $result = array();

        while ($stmt->fetch()) {
            $family = $this->arrayToObject($data, User::class);
            $result[] = $family;
        }

        $connection->close();
        return $result;
    }
}