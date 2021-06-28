<?php

require_once('BaseModel.php');

class UserModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'user';
        $this->pk = 'id';
    }

    function checkUserExists($email, $password, $roleId)
    {
        $query = "SELECT COUNT(*) AS `count` 
                    FROM $this->table 
                    WHERE $this->table.email = :email AND $this->table.password = :password 
                      AND $this->table.role_id = :roleId AND $this->table.validated = 1 
                      AND $this->table.end_date > CURRENT_TIMESTAMP()";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':roleId', $roleId);
        $stmt->execute();

        $row = $stmt->fetch();
        $userCount = $row['count'];

        if ($userCount != 1) {
            return null;
        }
        return true;
    }

    function getUserInfo($email, $password, $roleId)
    {
        $query = "SELECT user.id, user.name, user.surname  
                    FROM user 
                    WHERE user.email = :email AND user.password = :password AND user.role_id = :roleId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':roleId', $roleId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        return null;
    }

    function userTaken($email, $roleId)
    {
        $query = "SELECT user.id
                    FROM user 
                    WHERE user.email = :email AND user.role_id = :roleId";

        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':roleId', $roleId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    function getUserEmail($id)
    {
        $query = "SELECT user.email 
                    FROM user 
                    WHERE user.id = :userId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $id);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getUserName($id)
    {
        $query = "SELECT user.name
                    FROM user 
                    WHERE user.id = :userId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $id);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getUserNameSurname($userId)
    {
        $query = "SELECT name, surname  
                    FROM user 
                    WHERE user.id = :userId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        return null;
    }

    function getUserRole($userId)
    {
        $query = "SELECT role
                    FROM role
                    INNER JOIN user 
                    ON role.id = user.role_id
                    WHERE user.id = :userId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function requestUserAccount($email, $password, $name, $surname, $endDate, $roleId)
    {
        $query = "INSERT INTO user (email, password, name, surname, start_date, end_date, validated, role_id)
                    VALUES (:email, :password, :name, :surname, current_timestamp, :endDate, 0, :roleId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':surname', $surname);
        $stmt->bindValue(':endDate', $endDate);
        $stmt->bindValue(':roleId', $roleId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function changePassword($userId, $password)
    {
        $query = "UPDATE user 
                    SET user.password = :password
                    WHERE id = :userId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }
}
