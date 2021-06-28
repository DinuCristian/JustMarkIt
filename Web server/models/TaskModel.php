<?php

require_once('BaseModel.php');

class TaskModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'task';
        $this->pk = 'id';
    }

    function getTasks($assignmentId)
    {
        $query = "SELECT task.id, task.title, task.description, task.try, task.grade_percentage, submission.final_grade,
                        assignment.release_grade
                    FROM task 
                    LEFT JOIN submission
                    ON task.id = submission.task_id 
                    INNER JOIN assignment
                    ON task.assignment_id = assignment.id
                    WHERE task.assignment_id = :assignmentId AND (submission.final = 1 OR submission.final IS NULL) 
                    GROUP BY task.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getTaskTitle($id)
    {
        $query = "SELECT $this->table.title 
                    FROM $this->table 
                    WHERE $this->pk = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getTaskTry($id)
    {
        $query = "SELECT $this->table.try FROM $this->table WHERE $this->pk = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }
}