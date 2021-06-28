<?php

require_once('BaseModel.php');

class AdministratorModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'user';
        $this->pk = 'id';
    }

    function queryFilterBuilder($query, $filterProfessor, $filterStudent, $filterValidUser, $filterInvalidUser)
    {
        if ($filterValidUser == 'true' || $filterInvalidUser == 'true') {
            $query .= " AND (1 = 0";
            if ($filterValidUser == 'true') {
                $query .= " OR user.validated = 1";
            }
            if ($filterInvalidUser == 'true') {
                $query .= " OR user.validated = 0";
            }
            $query .= ")";
        }

        if ($filterProfessor == 'true' || $filterStudent == 'true') {
            $query .= " AND (1 = 0";
            if ($filterProfessor == 'true') {
                $query .= " OR user.role_id = 2";
            }
            if ($filterStudent == 'true') {
                $query .= " OR user.role_id = 3";
            }
            $query .= ")";
        }

        return $query;
    }

    function getUserRequestsCount($filterProfessor, $filterStudent, $filterValidUser, $filterInvalidUser)
    {
        $query = "SELECT COUNT(*) 
                    FROM $this->table 
                    INNER JOIN role 
                    ON $this->table.role_id = role.id 
                    WHERE 1 = 1";
        $query = $this->queryFilterBuilder($query, $filterProfessor, $filterStudent, $filterValidUser, $filterInvalidUser);

        $stmt = $this->connection->query($query);
        return $stmt->fetch()[0];
    }

    function getUserRequests($min, $max, $filterProfessor, $filterStudent, $filterValidUser, $filterInvalidUser)
    {
        $query = "SELECT $this->table.id, $this->table.email, $this->table.name, $this->table.surname, 
                    $this->table.end_date, $this->table.validated, role.role 
                    FROM $this->table 
                    INNER JOIN role 
                    ON $this->table.role_id = role.id 
                    WHERE 1 = 1 AND $this->table.id != 1
                    ORDER BY $this->table.id";

        $query = $this->queryFilterBuilder($query, $filterProfessor, $filterStudent, $filterValidUser, $filterInvalidUser);
        $query .= " LIMIT $min, $max";

        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':min', $min, PDO::PARAM_INT);
        $stmt->bindValue(':max', $max, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getUserAccountDetails($id)
    {
        $query = "SELECT $this->table.id, $this->table.email, $this->table.name, $this->table.surname, 
                    $this->table.end_date, $this->table.role_id 
                    FROM $this->table 
                    WHERE $this->table.id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        return null;
    }

    function updateUser($id, $name, $surname, $endDate, $role)
    {
        $query = "UPDATE $this->table 
                    SET $this->table.name = :name, $this->table.surname = :surname, $this->table.end_date = :endDate, 
                        $this->table.role_id = :role 
                    WHERE $this->table.id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':surname', $surname, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

    function deleteUserRequest($id)
    {
        $query = "DELETE FROM $this->table 
                    WHERE $this->table.id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function validateUserRequest($id)
    {
        $query = "UPDATE $this->table 
                    SET $this->table.validated = 1 
                    WHERE $this->table.id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function invalidateUser($id)
    {
        $query = "UPDATE $this->table 
                    SET $this->table.validated = 0
                    WHERE $this->table.id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function bulkEnrollment($users)
    {
        $query = "INSERT INTO user (email, password, name, surname, start_date, end_date, validated, role_id)
                    VALUES ";

        foreach ($users as $user)
        {
            $query .= "('". $user['email'] ."', '". $user['password'] ."', '". $user['name'] ."',
                        '". $user['surname'] ."', current_timestamp, '". $user['endDate'] ."', 1, 
                        ". $user['role'] ."),";
        }
        $query = substr_replace($query, ";", -1);
        $stmt = $this->connection->prepare($query);

        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            return $stmt->rowCount();
        }
        return null;
    }

    function getBulkEnrolledUsers()
    {
        $query = "SELECT email, name
                    FROM user
                    WHERE bulk_enrolled = 1";
        $stmt = $this->connection->prepare($query);

        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getCourses()
    {
        $query = "SELECT course.id, course.title, user.name, user.surname
                    FROM course
                    INNER JOIN user
                    ON course.leader = user.id";
        $stmt = $this->connection->prepare($query);

        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function removeUserFromCourse($studentId, $courseId)
    {
        $query = "DELETE FROM course_users
                    WHERE course_users.user_id = :userId AND course_users.course_id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $studentId, PDO::PARAM_INT);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function getUsersNotInCourse($courseId)
    {
        $query = "SELECT user.id, user.name, user.surname
                    FROM user
                    WHERE user.role_id = 3 AND user.validated = 1 AND user.id NOT IN
                        (SELECT course_users.user_id
                        FROM course_users
                        WHERE course_users.course_id = :courseId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function addUserToCourse($userId, $courseId)
    {
        $query = "INSERT INTO course_users (user_id, course_id) 
                VALUES (:userId, :courseId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }
}