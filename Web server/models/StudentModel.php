<?php

require_once('BaseModel.php');

class StudentModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'user';
        $this->pk = 'id';
    }

    function getStudentCourses($studentId)
    {
        $query = "SELECT course.id, course.title, course.description, course.year, course.semester
                    FROM course
                    INNER JOIN course_users
                    ON course.id = course_users.course_id
                    INNER JOIN user
                    ON course_users.user_id = user.id
                    WHERE user.id = :studentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':studentId', $studentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getCourseGrade($courseId)
    {
        $query = "SELECT submission.final_grade, task.grade_percentage AS task_percentage, 
                        assignment.grade_percentage AS assignment_percentage
                    FROM submission
                    INNER JOIN task
                    ON submission.task_id = task.id
                    INNER JOIN assignment
                    ON task.assignment_id = assignment.id
                    WHERE submission.final = 1 AND assignment.course_id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getAssignments($courseId)
    {
        $query = "SELECT id, title, description, due_date, release_grade
                    FROM assignment 
                    WHERE assignment.course_id = :courseId AND assignment.publish_assignment_date <= current_timestamp";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getAssignmentGrade($assignmentId, $userId)
    {
        $query = "SELECT submission.final_grade, task.grade_percentage, SUM(test.grade) AS max_task_grade
                    FROM submission
                    INNER JOIN task
                    ON submission.task_id = task.id
                    INNER JOIN test
                    ON task.id = test.task_id
                    WHERE submission.final = 1 AND task.assignment_id = :assignmentId AND submission.user_id = :userId
                    GROUP BY task.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getVisibleTests($taskId)
    {
        $query = "SELECT id, description, input_test, output_test, grade, test_type_id 
                    FROM test 
                    WHERE task_id = :taskId AND test_type_id = 1 AND coding_standard_id = 1";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getTestCodingStyle($taskId)
    {
        $query = "SELECT test.id, test.description, input_test, output_test, grade, test_type_id, coding_standard.name,
                    coding_standard.description AS url
                    FROM test 
                    INNER JOIN coding_standard
                    ON test.coding_standard_id = coding_standard.id
                    WHERE task_id = :taskId AND test_type_id = 1 AND coding_standard_id != 1";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function insertSubmission($userId, $taskId, $version)
    {
        $query = "INSERT INTO submission (date, version, user_id, task_id, final, submitted) 
                    VALUES (current_timestamp, :version, :userId, :taskId, 1, 1)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':version', $version, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $this->connection->lastInsertId();
        }
        return null;
    }

    function updateSubmission($submissionId, $userId, $taskId)
    {
        $query = "UPDATE submission SET final = 0 WHERE user_id = :userId AND task_id = :taskId
                    AND id != :submissionId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':submissionId', $submissionId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function setFinalSubmission($submissionId)
    {
        $query = "UPDATE submission SET final = 1 
                    WHERE id = :submissionId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':submissionId', $submissionId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function getTaskSubmissionsCount($userId, $taskId)
    {
        $query = "SELECT COUNT(*) FROM submission WHERE user_id = :userId AND task_id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getTaskDueDate($taskId)
    {
        $query = "SELECT due_date FROM assignment INNER JOIN task ON assignment.id = task.assignment_id 
                    WHERE task.id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getTaskSubmissions($taskId, $userId)
    {
        $query = "SELECT submission.id, date, version, partial_grade, final_grade, date, final, task.class_name, 
                        assignment.release_grade, assignment.due_date
                    FROM submission 
                    INNER JOIN task 
                    ON submission.task_id = task.id 
                    INNER JOIN assignment
                    ON task.assignment_id = assignment.id
                    WHERE task_id = :taskId AND user_id = :userId 
                    ORDER BY version DESC";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getTaskClassName($taskId)
    {
        $query = "SELECT class_name 
                    FROM task 
                    WHERE id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch()[0];
        }
        return null;
    }

    function getSubmissionTestOutput($submissionId, $userId)
    {
        $query = "SELECT submission.version, test.description, test_type.type, test.input_test, test.output_test, 
                    test.coding_standard_id, output.output, output.status, test.grade, 
                    test.coding_standard_id AS coding_standard
                    FROM submission 
                    INNER JOIN output ON submission.id = output.submission_id
                    INNER JOIN test ON output.test_id = test.id
                    INNER JOIN test_type ON test.test_type_id = test_type.id
                    WHERE submission.id = :submissionId AND submission.user_id = :userId
                    GROUP BY test.coding_standard_id, test.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':submissionId', $submissionId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getNavigationDetailsTestOutput($submissionId)
    {
        $query = "SELECT course.id, course.title, assignment.id, assignment.title, task.id, task.title
                    FROM submission 
                    INNER JOIN task
                    ON submission.task_id = task.id
                    INNER JOIN assignment
                    ON task.assignment_id = assignment.id
                    INNER JOIN course
                    ON assignment.course_id = course.id
                    WHERE submission.id = :submissionId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':submissionId', $submissionId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        return null;
    }

    function getTaskOutput($assignmentId, $userId)
    {
        $query = "SELECT task.id AS task_id, submission.version, submission.final_grade, test.description, 
                    test_type.type, test.input_test, test.output_test, test.grade, 
                    test.coding_standard_id AS coding_standard, output.output, output.status, task.class_name, 
                    task.grade_percentage, coding_standard.name AS coding_name, coding_standard.description AS url
                    FROM submission
                    INNER JOIN output 
                    ON submission.id = output.submission_id
                    INNER JOIN test 
                    ON output.test_id = test.id
                    INNER JOIN test_type 
                    ON test.test_type_id = test_type.id
                    INNER JOIN task
                    ON submission.task_id = task.id
                    INNER JOIN coding_standard
                    ON test.coding_standard_id = coding_standard.id
                    WHERE task.assignment_id = :assignmentId AND submission.user_id = :userId AND submission.final = 1
                    ORDER BY task.class_name, test.coding_standard_id, test.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getAssignmentGradesForStudents($assignmentId)
    {
        $query = "SELECT g.user_id, g.name, g.surname, SUM(g.grade_percentage / g.max_task_grade * g.final_grade) AS grade
                    FROM
                        (SELECT submission.user_id, submission.final_grade, user.name, user.surname, task.grade_percentage, 
                                SUM(test.grade) AS max_task_grade
                        FROM submission
                        INNER JOIN user
                        ON submission.user_id = user.id
                        INNER JOIN task
                        ON submission.task_id = task.id
                        INNER JOIN test
                        ON task.id = test.task_id
                        WHERE submission.final = 1 AND task.assignment_id = :assignmentId
                        GROUP BY submission.user_id, task.id) AS g
                    GROUP BY g.user_id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getAssignmentSubmissionNo($assignmentId, $userId)
    {
        $query = "SELECT task.id, COUNT(submission.id) AS used_try, task.try
                    FROM submission
                    INNER JOIN task
                    ON submission.task_id = task.id
                    WHERE submission.user_id = :userId and task.assignment_id = :assignmentId
                    GROUP BY task.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function checkIfStudentSubmittedCode($userId, $taskId)
    {
        $query = "SELECT submitted
                    FROM submission
                    WHERE user_id = :userId AND task_id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()[0] == 1) {
            return true;
        }
        return false;
    }
}