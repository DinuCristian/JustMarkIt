<?php

require_once('BaseModel.php');

class ProfessorModel extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'course';
        $this->pk = 'id';
    }

    function getProfessorCourses($professorId)
    {
        $query = "SELECT * 
                    FROM $this->table 
                    WHERE course.leader = :professorId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':professorId', $professorId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getEnrolledStudents($courseId)
    {
        $query = "SELECT user.id, user.email, user.name, user.surname 
                    FROM user 
                    INNER JOIN course_users 
                    ON user.id = course_users.user_id 
                    WHERE course_users.course_id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getEnrolledStudentsCount($courseId)
    {
        $query = "SELECT COUNT(*) FROM user INNER JOIN course_users ON user.id = course_users.user_id 
                    WHERE course_users.course_id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function deleteCourse($courseId)
    {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $courseId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function insertCourse($title, $description, $year, $semester, $leader)
    {
        $query = "INSERT INTO $this->table (title, description, year, semester, leader) 
                VALUES (:title, :description, :year, :semester, :leader)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':year', $year, PDO::PARAM_STR);
        $stmt->bindValue(':semester', $semester, PDO::PARAM_STR);
        $stmt->bindValue(':leader', $leader, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function getCourseDetails($id)
    {
        $query = "SELECT title, description, year, semester FROM $this->table WHERE course.id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        return null;
    }

    function updateCourse($courseId, $title, $description, $year, $semester)
    {
        $query = "UPDATE $this->table 
                    SET title = :title, description = :description, year = :year, semester = :semester 
                    WHERE id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':year', $year, PDO::PARAM_STR);
        $stmt->bindValue(':semester', $semester, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function getAssignmentDetails($assignmentId)
    {
        $query = "SELECT title, description, due_date, publish_assignment_date, grade_percentage
                    FROM assignment 
                    WHERE assignment.id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result[0];
    }

    function insertAssignment($title, $description, $publishDate, $dueDate, $releaseDate, $assignmentPercentage, $courseId)
    {
        $query = "INSERT INTO assignment (title, description, due_date, publish_assignment_date, release_grade_date, grade_percentage, course_id) 
                VALUES (:title, :description, :dueDate, :publishDate, DATE_ADD(:releaseDate, INTERVAL 7 day), :assignmentPercentage, :courseId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':dueDate', $dueDate);
        $stmt->bindValue(':releaseDate', $releaseDate);
        $stmt->bindValue(':publishDate', $publishDate);
        $stmt->bindValue(':assignmentPercentage', $assignmentPercentage);
        $stmt->bindValue(':courseId', $courseId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function updateAssignment($assignmentId, $title, $description, $dueDate, $releaseDate, $publishDate, $assignmentPercentage)
    {
        $query = "UPDATE assignment 
                    SET title = :title, description = :description, due_date = :dueDate, publish_assignment_date = :publishDate,
                        release_grade_date = DATE_ADD(:releaseDate, INTERVAL 7 day), grade_percentage = :assignmentPercentage
                    WHERE id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':dueDate', $dueDate);
        $stmt->bindValue(':releaseDate', $releaseDate);
        $stmt->bindValue(':publishDate', $publishDate);
        $stmt->bindValue(':assignmentPercentage', $assignmentPercentage);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function deleteAssignment($assignmentId)
    {
        $query = "DELETE FROM assignment WHERE id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $assignmentId);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function getTaskDetails($taskId)
    {
        $query = "SELECT title, description, class_name, try, grade_percentage
                    FROM task 
                    WHERE task.id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result[0];
    }

    function insertTask($title, $description, $className, $try,$taskPercentage, $assignmentId)
    {
        $query = "INSERT INTO task (title, description, class_name, try, grade_percentage, assignment_id) 
                VALUES (:title, :description, :className, :try, :taskPercentage, :assignmentId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':className', $className);
        $stmt->bindValue(':try', $try, PDO::PARAM_INT);
        $stmt->bindValue(':taskPercentage', $taskPercentage, PDO::PARAM_INT);
        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function updateTask($taskId, $title, $description, $className, $try, $taskPercentage)
    {
        $query = "UPDATE task 
                    SET title = :title, description = :description, class_name = :className, try = :try, 
                        grade_percentage = :taskPercentage
                    WHERE id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':className', $className);
        $stmt->bindValue(':try', $try, PDO::PARAM_INT);
        $stmt->bindValue(':taskPercentage', $taskPercentage, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function deleteTask($taskId)
    {
        $query = "DELETE FROM task WHERE id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $taskId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function getTests($taskId)
    {
        $query = "SELECT test.id, test_type.type, description, input_test, output_test, grade
                    FROM test
                    INNER JOIN test_type
                    ON test.test_type_id = test_type.id
                    WHERE test.task_id = :taskId AND test.coding_standard_id = 1
                    GROUP BY test.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getTestCodingStyle($taskId)
    {
        $query = "SELECT test.id, test_type.type, test.description, input_test, output_test, 
                    grade, coding_standard.name, coding_standard.description AS url
                    FROM test
                    INNER JOIN test_type
                    ON test.test_type_id = test_type.id
                    INNER JOIN coding_standard
                    ON coding_standard.id = test.coding_standard_id
                    WHERE test.task_id = :taskId AND test.coding_standard_id != 1
                    GROUP BY test.id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getTestDetails($testId)
    {
        $query = "SELECT description, input_test, output_test, grade, test_type.type, coding_standard_id AS coding
                    FROM test
                    INNER JOIN test_type
                    ON test.test_type_id = test_type.id
                    WHERE test.id = :testId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':testId', $testId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result[0];
    }

    function insertTest($description, $inputTest, $outputTest, $grade, $visible, $taskId)
    {
        $query = "INSERT INTO test (description, input_test, output_test, grade, test_type_id, task_id, coding_standard_id) 
                VALUES (:description, :inputTest, :outputTest, :grade, :visible, :taskId, 1)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':inputTest', $inputTest, PDO::PARAM_STR);
        $stmt->bindValue(':outputTest', $outputTest, PDO::PARAM_STR);
        $stmt->bindValue(':grade', $grade, PDO::PARAM_INT);
        $stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function insertTestCodingStyle($inputTest, $outputTest, $grade, $visible, $taskId, $coding)
    {
        $query = "INSERT INTO test (description, input_test, output_test, grade, test_type_id, task_id, coding_standard_id) 
                VALUES ('coding style', :inputTest, :outputTest, :grade, :visible, :taskId, :coding)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':inputTest', $inputTest, PDO::PARAM_INT);
        $stmt->bindValue(':outputTest', $outputTest, PDO::PARAM_INT);
        $stmt->bindValue(':grade', $grade, PDO::PARAM_INT);
        $stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->bindValue(':coding', $coding, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function updateTest($testId, $description, $inputTest, $outputTest, $grade, $visible)
    {
        $query = "UPDATE test 
                    SET description = :description, input_test = :inputTest, output_test = :outputTest, grade = :grade, 
                    test_type_id = :visible 
                    WHERE id = :testId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':testId', $testId, PDO::PARAM_INT);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':inputTest', $inputTest, PDO::PARAM_STR);
        $stmt->bindValue(':outputTest', $outputTest, PDO::PARAM_STR);
        $stmt->bindValue(':grade', $grade, PDO::PARAM_INT);
        $stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function updateTestCodingStyle($testId, $inputTest, $outputTest, $grade, $visible, $coding)
    {
        $query = "UPDATE test 
                    SET input_test = :inputTest, output_test = :outputTest, grade = :grade, test_type_id = :visible,
                        coding_standard_id = :coding
                    WHERE id = :testId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':testId', $testId, PDO::PARAM_INT);
        $stmt->bindValue(':inputTest', $inputTest, PDO::PARAM_INT);
        $stmt->bindValue(':outputTest', $outputTest, PDO::PARAM_INT);
        $stmt->bindValue(':grade', $grade, PDO::PARAM_INT);
        $stmt->bindValue(':visible', $visible, PDO::PARAM_INT);
        $stmt->bindValue(':coding', $coding, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function deleteTest($testId)
    {
        $query = "DELETE 
                    FROM test 
                    WHERE id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $testId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function deleteTestCodingStyle($testId)
    {
        $query = "DELETE 
                    FROM test 
                    WHERE id = :id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':id', $testId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    function getSubmissionsPerTaskCount($taskId)
    {
        $query = "SELECT COUNT(*) 
                    FROM submission 
                    WHERE task_id = :taskId 
                    GROUP BY user_id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getSubmissionsPerAssignmentCount($assignmentId)
    {
        $query = "SELECT COUNT(*) FROM (SELECT user_id FROM submission INNER JOIN task ON submission.task_id = task.id 
                    WHERE task.assignment_id = :assignmentId GROUP BY submission.user_id) AS subquery";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getTestsPerTaskCount($taskId)
    {
        $query = "SELECT COUNT(*) FROM test WHERE task_id = :taskId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getTasksPerAssignmentCount($assignmentId)
    {
        $query = "SELECT COUNT(*) FROM task WHERE assignment_id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getAssignmentsPerCourseCount($courseId)
    {
        $query = "SELECT COUNT(*) 
                    FROM assignment 
                    WHERE course_id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function runAllTest($taskId, $assignmentId)
    {
        $query = "UPDATE submission 
                    SET marking_server_professor = 1 
                    WHERE task_id = :taskId AND submitted = 1 AND final = 1";
        $query1 = "UPDATE assignment 
                    SET release_grade = 0 
                    WHERE id = :assignmentId";
        $stmt = $this->connection->prepare($query);
        $stmt1 = $this->connection->prepare($query1);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt1->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt1->execute();

        if ($stmt->rowCount() != 0) {
            return true;
        }
        return null;
    }

    function releaseAssignmentGrade($assignmentId)
    {
        $query = "UPDATE assignment 
                    SET release_grade = 1 
                    WHERE id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            return true;
        }
        return null;
    }

    function releaseCourseGrade($courseId)
    {
        $query = "UPDATE 
                    course 
                    SET release_grade = 1 
                    WHERE id = :courseId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            return true;
        }
        return null;
    }

    function getRunningTestsProfessorForTask($taskId)
    {
        $query = "SELECT COUNT(*) 
                    FROM submission 
                    WHERE task_id = :taskId AND marking_server_professor = 1";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getStudentsDidntSubmitCodePerTask($taskId, $courseId)
    {
        $query = "SELECT course_users.user_id
                    FROM course_users
                    INNER JOIN user
                    ON course_users.user_id = user.id
                    WHERE course_users.course_id = :courseId 
                      AND course_users.user_id NOT IN (SELECT submission.user_id
                                                        FROM submission
                                                        WHERE submission.task_id = :taskId)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function setFinalGradeStudentsDidntSubmitCode($getStudentsDidntSubmitCodePerTask, $taskId)
    {
        $query = "INSERT INTO submission (user_id, task_id, date, version, final, marking_server_student, final_grade, submitted)
                    VALUES ";

        foreach ($getStudentsDidntSubmitCodePerTask as $user)
        {
            $query .= "('". $user['user_id'] ."', '". $taskId ."', current_timestamp, 1, 1, 1, 0, 0),";
        }
        $query = substr_replace($query, ";", -1);


        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            return true;
        }
        return null;
    }

    function getForeignKeysForTaskWithNoSubmittedCode($taskId)
    {
        $query = "SELECT submission.id AS submission_id, test.id AS test_id
                    FROM submission
                    INNER JOIN task
                    ON submission.task_id = task.id
                    INNER JOIN test
                    ON task.id = test.task_id
                    WHERE submission.task_id = :taskId AND submission.submitted = 0";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function setOutputStudentsDidntSubmitCode($foreignKeysForTaskWithNoSubmittedCode)
    {
        $query = "INSERT INTO output (submission_id, test_id, status, output)
                    VALUES ";

        foreach ($foreignKeysForTaskWithNoSubmittedCode as $foreignKey)
        {
            $query .= "('". $foreignKey['submission_id'] ."', '". $foreignKey['test_id'] ."', 0, 'No submission'),";
        }
        $query = substr_replace($query, ";", -1);


        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            return true;
        }
        return null;
    }

    function getTaskCountPerAssignment($assignmentId)
    {
        $query = "SELECT COUNT(task.id) AS task_count
                    FROM task
                    WHERE task.assignment_id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getTaskGradesForStudentsPerAssignment($assignmentId)
    {
        $query = "SELECT g.task_id AS id, g.task_title AS title, g.user_id, g.name, g.surname, SUM(100 / g.max_task_grade * g.final_grade) AS grade
                    FROM
                        (SELECT submission.user_id, submission.final_grade, user.name, user.surname, task.grade_percentage, 
                                SUM(test.grade) AS max_task_grade, task.id AS task_id, task.title AS task_title 
                        FROM submission
                        INNER JOIN user
                        ON submission.user_id = user.id
                        INNER JOIN task
                        ON submission.task_id = task.id
                        INNER JOIN test
                        ON task.id = test.task_id
                        WHERE submission.final = 1 AND task.assignment_id = :assignmentId 
                        GROUP BY submission.user_id, task.id) AS g
                    GROUP BY g.task_id, g.user_id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getCourseId($assignmentId)
    {
        $query = "SELECT course_id
                    FROM assignment 
                    WHERE assignment.id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getSubmissionsForTaskCount($taskId)
    {
        $query = "SELECT COUNT(id)
                    FROM submission
                    WHERE task_id = :taskId AND submitted = 1";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getTaskIdsForAssignment($assignmentId)
    {
        $query = "SELECT id
                    FROM task
                    WHERE task.assignment_id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getTaskMeanGrades($assignmentId)
    {
        $query = "SELECT g.task_id, g.title, AVG(g.final_grade) * 100 / h.max_task_grade AS mean_grade
                    FROM
                        (SELECT task.id AS task_id, task.title, submission.final_grade
                        FROM submission
                        INNER JOIN task
                        ON submission.task_id = task.id
                        WHERE submission.final = 1
                        GROUP BY task.id, submission.user_id) AS g
                    INNER JOIN
                        (SELECT test.task_id, SUM(test.grade) as max_task_grade
                        FROM test
                        GROUP BY test.task_id) AS h
                    ON g.task_id = h.task_id
                    WHERE g.task_id IN (SELECT task.id
                                           FROM task
                                           WHERE task.assignment_id = :assignmentId)
                    GROUP BY g.task_id";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getUnmarkedSubmissionsPerAssignmentCount($assignmentId)
    {
        $query = "SELECT COUNT(submission.id) AS unmarked_submissions
                    FROM submission
                    INNER JOIN task
                    ON submission.task_id = task.id
                    WHERE submission.final_grade IS NULL AND submission.final = 1 
                      AND task.assignment_id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function getRunningTestsProfessorForAssignment($assignmentId)
    {
        $query = "SELECT COUNT(*)
                    FROM submission
                    INNER JOIN task
                    ON submission.task_id = task.id
                    WHERE task.assignment_id= :assignmentId AND marking_server_professor = 1";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()[0];
    }

    function checkReleaseAssignmentGrades($assignmentId) {
        $query = "SELECT assignment.release_grade
                    FROM assignment
                    WHERE assignment.id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()[0] == 1) {
            return true;
        }
        return false;
    }

    function setReleaseGradeToFalse($assignmentId)
    {
        $query = "UPDATE assignment 
                    SET release_grade = 0
                    WHERE id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return null;
    }

    function getCodingStandards()
    {
        $query = "SELECT id, name, description
                    FROM coding_standard
                    WHERE id != 1";
        $stmt = $this->connection->prepare($query);

        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch()) {
            $result[] = $row;
        }
        return $result;
    }

    function getNavigationDetailsAssignmentReport($assignmentId)
    {
        $query = "SELECT course.id, course.title, assignment.id, assignment.title
                    FROM course
                    INNER JOIN assignment 
                    ON assignment.course_id = course.id
                    WHERE assignment.id = :assignmentId";
        $stmt = $this->connection->prepare($query);

        $stmt->bindValue(':assignmentId', $assignmentId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        }
        return null;
    }
}