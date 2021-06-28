<?php

require_once('BaseModel.php');

class AssignmentModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'assignment';
        $this->pk = 'id';
    }

    function getAssignments($courseId)
    {
        $query = "SELECT id, title, description, grade_percentage, due_date, publish_assignment_date, grade_percentage,
                    release_grade
                    FROM assignment 
                    WHERE assignment.course_id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getAssignmentTitle($assignmentId)
    {
        $query = "SELECT $this->table.title 
                    FROM $this->table 
                    WHERE $this->pk = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $assignmentId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getAssignmentDeadline($assignmentId)
    {
        $query = "SELECT $this->table.due_date
                    FROM $this->table 
                    WHERE $this->pk = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $assignmentId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getTaskTitlePerAssignment($assignmentId)
    {
        $query = "SELECT title
                    FROM task
                    WHERE task.assignment_id = :assignmentId
                    GROUP BY task.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function checkGradesReleased($assignmentId)
    {
        $query = "SELECT release_grade
                    FROM assignment
                    WHERE assignment.id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch()[0];

        if ($result == 1) {
            return true;
        }
        return false;
    }
}