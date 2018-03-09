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
        $stmt = $connection->prepare("SELECT u.nickName,
                                                    u.email,
                                                    u.firstName,
                                                    u.lastName,
                                                    u.avatar,
                                                    up.type AS userType
                                                    FROM users u
                                              INNER JOIN user_privileges up ON u.id = up.user
                                              WHERE u.id = ?");
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

    /**
     * Insert new member to users table
     *
     * @param User $user
     * @return boolean
     */
    public function addNewUser($user)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("INSERT INTO users(nickName, email, password, firstName, lastName, avatar)
                                              VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $user->nickName, $user->email, $user->firstName, $user->lastName, $user->image);
        $stmt->execute();

        $isSucceed = $stmt->affected_rows > 0;

        if ($isSucceed) {
            $userId = $stmt->insert_id;

            $stmt->prepare("INSERT INTO user_privileges(user, type) VALUES (?,?)");
            $stmt->bind_param("ii", $userId, $user->userType);
            $stmt->execute();

            $isSucceed = $stmt->affected_rows > 0;
        }

        $connection->close();
        return $isSucceed;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function updateUser($user)
    {
        $connection = $this->createConnection();
        $stmt = $connection->prepare("UPDATE users 
                                              SET email = ?,
                                                  firstName = ?,
                                                  lastName = ?,
                                                  avatar = ?
                                              WHERE id = ?");
        $stmt->bind_param("ssss", $user->email, $user->firstName, $user->lastName, $user->image);
        $isSucceed = $stmt->affected_rows > 0;

        if ($isSucceed) {
            $userId = $stmt->insert_id;

            $stmt->prepare("UPDATE user_privileges
                                    SET type = ?
                                    WHERE user = ?");
            $stmt->bind_param("ii", $userId, $user->userType);
            $stmt->execute();

            $isSucceed = $stmt->affected_rows > 0;
        }

        $connection->close();
        return $isSucceed;
    }
}