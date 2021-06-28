<?php

require_once('BaseModel.php');

class CourseModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'course';
        $this->pk = 'id';
    }

    function getCourseTitle($courseId)
    {
        $query = "SELECT $this->table.title 
                    FROM $this->table 
                    WHERE $this->pk = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $courseId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }
}