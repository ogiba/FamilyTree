<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 25.09.2017
 * Time: 21:13
 */

namespace Database;

class LoginManager extends BaseDatabaseManager
{
    public function loginUser($userName, $password) {
        $hashedPassword = hash ( "sha256" , $password ,false);

        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM users WHERE nickName = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();

        $userData = $this->bindResult($stmt);

        if ($stmt->fetch()){
            if($userData["password"] == $hashedPassword){
                $connection->close();
                $loginToken = $this->insertAttempt($userData["id"], $userData["nickName"]);
                return $loginToken;
            }
        }

        return null;
    }

    private function insertAttempt($id, $username) {
        $token = md5(uniqid($username, true));

        $connection = $this->createConnection();

        $stmt = $connection->prepare("SELECT * FROM login_attempts WHERE user = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data= $this->bindResult($stmt);

        if($stmt->fetch()) {
            return $data["token"];
        } else {
            $stmt->reset();
            $stmt = $connection->prepare("INSERT INTO login_attempts(user, token) VALUES (?,?)");
            $stmt->bind_param("is", $id, $token);
            $stmt->execute();
        }

        return $token;
    }
}