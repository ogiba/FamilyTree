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
    public function loginUser($userName, $password)
    {
        $hashedPassword = hash("sha256", $password, false);

        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM users u
                                              INNER JOIN user_privileges up ON u.id = up.user
                                              WHERE (u.nickName = ? OR u.email = ?) AND up.type = 1");
        $stmt->bind_param("ss", $userName, $userName);
        $stmt->execute();

        $userData = $this->bindResult($stmt);

        if ($stmt->fetch()) {
            if ($userData["password"] == $hashedPassword) {
                $connection->close();
                $loginToken = $this->insertAttempt($userData["id"], $userData["nickName"]);
                return $loginToken;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function logoutUser()
    {
        if (is_null($_SESSION["token"]) || !isset($_SESSION["token"])) {
            return false;
        }
        $currentToken = $_SESSION["token"];

        $connection = $this->createConnection();
        $stmt = $connection->prepare("DELETE FROM login_attempts WHERE token = ?");
        $stmt->bind_param("s", $currentToken);
        $stmt->execute();

        $_SESSION["token"] = null;
        return true;
    }

    public function checkUserPrivilegesByToken($token)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("SELECT * FROM user_privileges up 
                                              INNER JOIN login_attempts la
                                              WHERE la.token = ? 
                                              AND up.user = la.user
                                              AND up.type = 1 ");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch() && !is_null($data)) {
            return true;
        } else {
            return false;
        }
    }

    private function insertAttempt($id, $username)
    {
        $token = md5(uniqid($username, true));

        $connection = $this->createConnection();

        $stmt = $connection->prepare("SELECT * FROM login_attempts WHERE user = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $data = $this->bindResult($stmt);

        if ($stmt->fetch()) {
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
