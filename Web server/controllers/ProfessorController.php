<?php

require_once('controllers/Emailer.php');
require_once('models/ProfessorModel.php');
require_once('models/AssignmentModel.php');
require_once('models/CourseModel.php');
require_once('models/TaskModel.php');
require_once('settings/roles.php');

class ProfessorController
{
    public function viewCourses()
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Courses';

        require('views/common/header.php');

        $professorId = $_SESSION['userId'];

        $professorModel = new ProfessorModel();
        $_SESSION['courses'] = $professorModel->getProfessorCourses($professorId);

        require('views/professor/professor.php');
        require('views/common/footer.php');
    }

    public function viewEnrolledStudents($courseId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Enrolled Students';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $_SESSION['enrolledStudents'] = $professorModel->getEnrolledStudents($courseId);
        $_SESSION['studentCount'] = $professorModel->getEnrolledStudentsCount($courseId);

        require('views/professor/enrolledStudents.php');
        require('views/common/footer.php');
    }

    public function addCourse($go)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Add Course';

        require('views/common/header.php');

        if ($go) {
            $title = $_SESSION['title'] = $_POST['title'];
            $description = $_SESSION['description'] = $_POST['description'];
            $year = $_SESSION['year'] = $_POST['year'];
            $semester = $_SESSION['semester'] = $_POST['semester'];
            $leaderId = $_SESSION['userId'];

            if ($title == '') {
                $_SESSION['error_message'] = 'Type course title!';
                require('views/professor/addCourse.php');
                exit();
            } elseif ($description == '') {
                $_SESSION['error_message'] = 'Type course description!';
                require('views/professor/addCourse.php');
                exit();
            } elseif ($year == '') {
                $_SESSION['error_message'] = 'Type course year!';
                require('views/professor/addCourse.php');
                exit();
            } elseif ($semester == '') {
                $_SESSION['error_message'] = 'Type course semester!';
                require('views/professor/addCourse.php');
                exit();
            }

            $professorModel = new ProfessorModel();
            $result = $professorModel->insertCourse($title, $description, $year, $semester, $leaderId);
            if ($result == null) {
                $_SESSION['error_message'] = 'Error inserting course!';
                require('views/professor/addCourse.php');
                exit();
            }

            $_SESSION['ok_message'] = 'Course added successfully';
            header('Location: index.php?action=professor');
        } else {
            require('views/professor/addCourse.php');
        }
        require('views/common/footer.php');
    }

    public function editCourse($go, $courseId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Edit Course';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $result = $professorModel->getCourseDetails($courseId);

        $oldTitle = '';
        $oldDescription = '';
        $oldYear = '';
        $oldSemester = '';

        if ($result != null) {
            $_SESSION['courseId'] = $courseId;
            $oldTitle = $_SESSION['title'] = $result['title'];
            $oldDescription = $_SESSION['description'] = $result['description'];
            $oldYear = $_SESSION['year'] = $result['year'];
            $oldSemester = $_SESSION['semester'] = $result['semester'];
        }

        if ($go) {
            $courseId = $_SESSION['courseId'] = $_GET['id'];
            $title = $_SESSION['title'] = $_GET['title'];
            $description = $_SESSION['description'] = $_GET['description'];
            $year = $_SESSION['year'] = $_GET['year'];
            $semester = $_SESSION['semester'] = $_GET['semester'];

            if ($title == '') {
                $_SESSION['error_message'] = 'Type course title!';
                require('views/professor/editCourse.php');
                exit();
            } elseif ($description == '') {
                $_SESSION['error_message'] = 'Type course description!';
                require('views/professor/editCourse.php');
                exit();
            } elseif ($year == '') {
                $_SESSION['error_message'] = 'Type course year!';
                require('views/professor/editCourse.php');
                exit();
            } elseif ($semester == '') {
                $_SESSION['error_message'] = 'Type course semester!';
                require('views/professor/editCourse.php');
                exit();
            }

            if ($title != $oldTitle || $description != $oldDescription || $year != $oldYear ||
                $semester != $oldSemester) {
                $professorModel = new ProfessorModel();
                $result = $professorModel->updateCourse($courseId, $title, $description, $year, $semester);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error updating course!';
                    require('views/professor/editCourse.php');
                    exit();
                }
            }

            $_SESSION['ok_message'] = 'Course updated successfully';
            header('Location: index.php?action=professor');
        } else {
            require('views/professor/editCourse.php');
        }
        require('views/common/footer.php');
    }

    public function deleteCourse($courseId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $professorModel = new ProfessorModel();

        if ($professorModel->getEnrolledStudentsCount($courseId) == 0) {
            if ($professorModel->getAssignmentsPerCourseCount($courseId) == 0) {
                if ($professorModel->deleteCourse($courseId)) {
                    $_SESSION['ok_message'] = 'Course deleted.';
                } else {
                    $_SESSION['error_message'] = 'Error deleting course!';
                }
            } else {
                $_SESSION['error_message'] = 'This course can not be deleted because there are assignments added to
                                                    it!';
            }
        } else {
            $_SESSION['error_message'] = 'This course can not be deleted because there are students enrolled!';
        }

        $professorController = new ProfessorController();
        $professorController->viewCourses();
    }

    public function viewAssignments($courseId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Assignments';

        require('views/common/header.php');

        $courseModel = new CourseModel();
        $_SESSION['courseTitle'] = $courseModel->getCourseTitle($courseId);
        $assignmentModel = new AssignmentModel();
        $_SESSION['assignments'] = $assignmentModel->getAssignments($courseId);
        $professorModel = new ProfessorModel();
        $_SESSION['enrolledStudents'] = $professorModel->getEnrolledStudentsCount($courseId);

        $assignments = $_SESSION['assignments'];
        $submissions = array();
        $unmarkedSubmissionsCount = array();
        $runningTestsProfessor = array();
        foreach ($assignments as $assignment) {
            $submissions[] = $professorModel->getSubmissionsPerAssignmentCount($assignment['id']);
            $unmarkedSubmissionsCount[] = $professorModel->getUnmarkedSubmissionsPerAssignmentCount($assignment['id']);
            $runningTestsProfessor[] = $professorModel->getRunningTestsProfessorForAssignment($assignment['id']);
        }

        $_SESSION['submissions'] = $submissions;
        $_SESSION['unmarkedSubmissionsCount'] = $unmarkedSubmissionsCount;
        $_SESSION['runningTestsProfessor'] = $runningTestsProfessor;
        $_SESSION['courseId'] = $courseId;

        require('views/professor/viewAssignments.php');
        require('views/common/footer.php');
    }

    public function addAssignment($go, $courseId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Add Assignment';

        $_SESSION['courseId'] = $courseId;

        require('views/common/header.php');

        if ($go) {
            $title = $_SESSION['title'] = $_POST['title'];
            $description = $_SESSION['description'] = $_POST['description'];
            $dueDate = $_SESSION['dueDate'] = $_POST['dueDate'];
            $publishDate = $_SESSION['publishDate'] = $_POST['publishDate'];
            $assignmentPercentage = $_SESSION['assignmentPercentage'] = $_POST['assignmentPercentage'];

            if ($title == '') {
                $_SESSION['error_message'] = 'Type course title!';
                require('views/professor/addAssignment.php');
                exit();
            } elseif ($description == '') {
                $_SESSION['error_message'] = 'Type course description!';
                require('views/professor/addAssignment.php');
                exit();
            } elseif ($publishDate == '') {
                $_SESSION['error_message'] = 'Type publish date!';
                require('views/professor/addAssignment.php');
                exit();
            } elseif ($dueDate == '') {
                $_SESSION['error_message'] = 'Type due date!';
                require('views/professor/addAssignment.php');
                exit();
            } elseif ($publishDate >= $dueDate) {
                $_SESSION['error_message'] = 'Due date must be after publish date!';
                require('views/professor/addAssignment.php');
                exit();
            } elseif ($assignmentPercentage == '') {
                $_SESSION['error_message'] = 'Type the assignment percentage!';
                require('views/professor/addAssignment.php');
                exit();
            }

            $releaseDate = substr($dueDate, 0, 10);
            $professorModel = new ProfessorModel();
            $result = $professorModel->insertAssignment($title, $description, $publishDate, $dueDate, $releaseDate, $assignmentPercentage, $courseId);
            if ($result == null) {
                $_SESSION['error_message'] = 'Error inserting assignment!';
                require('views/professor/addAssignment.php');
                exit();
            }

            $_SESSION['ok_message'] = 'Assignment added successfully';
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
        } else {
            require('views/professor/addAssignment.php');
        }
        require('views/common/footer.php');
    }

    public function editAssignment($go, $assignmentId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Edit Assignment';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $result = $professorModel->getAssignmentDetails($assignmentId);

        $oldTitle = '';
        $oldDescription = '';
        $oldDueDate = '';
        $oldPublishDate = '';
        $oldAssignmentPercentage = '';

        if ($result != null) {
            $_SESSION['assignmentId'] = $assignmentId;
            $oldTitle = $_SESSION['title'] = $result['title'];
            $oldDescription = $_SESSION['description'] = $result['description'];
            $oldDueDate = $_SESSION['dueDate'] = $result['due_date'];
            $oldPublishDate = $_SESSION['publishDate'] = $result['publish_assignment_date'];
            $oldAssignmentPercentage = $_SESSION['assignmentPercentage'] = $result['grade_percentage'];
        }

        if ($go) {
            $courseId = $_SESSION['courseId'];
            $assignmentId = $_SESSION['assignmentId'] = $_GET['id'];
            $title = $_SESSION['title'] = $_GET['title'];
            $description = $_SESSION['description'] = $_GET['description'];
            $dueDate = $_SESSION['dueDate'] = $_GET['dueDate'];
            $publishDate = $_SESSION['publishDate'] = $_GET['publishDate'];
            $assignmentPercentage = $_SESSION['assignmentPercentage'] = $_GET['assignmentPercentage'];

            if ($title == '') {
                $_SESSION['error_message'] = 'Type course title!';
                require('views/professor/editAssignment.php');
                exit();
            } elseif ($description == '') {
                $_SESSION['error_message'] = 'Type course description!';
                require('views/professor/editAssignment.php');
                exit();
            } elseif ($publishDate == '') {
                $_SESSION['error_message'] = 'Select publish date!';
                require('views/professor/editAssignment.php');
                exit();
            } elseif ($dueDate == '') {
                $_SESSION['error_message'] = 'Select due date!';
                require('views/professor/editAssignment.php');
                exit();
            } elseif ($publishDate >= $dueDate) {
                $_SESSION['error_message'] = 'Due date must be after publish date!';
                require('views/professor/addAssignment.php');
                exit();
            } elseif ($assignmentPercentage == '') {
                $_SESSION['error_message'] = 'Type the assignment percentage!';
                require('views/professor/editAssignment.php');
                exit();
            }

            if ($title != $oldTitle || $description != $oldDescription || $dueDate != $oldDueDate ||
                $publishDate != $oldPublishDate || $assignmentPercentage != $oldAssignmentPercentage) {
                $releaseDate = substr($dueDate, 0, 10);
                $professorModel = new ProfessorModel();
                $result = $professorModel->updateAssignment($assignmentId, $title, $description, $dueDate, $releaseDate, $publishDate, $assignmentPercentage);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error updating assignment!';
                    require('views/professor/editAssignment.php');
                    exit();
                }
            }

            $_SESSION['ok_message'] = 'Assignment updated successfully';
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
        } else {
            require('views/professor/editAssignment.php');
        }
        require('views/common/footer.php');
    }

    public function deleteAssignment($assignmentId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $professorModel = new ProfessorModel();

        if ($professorModel->getTasksPerAssignmentCount($assignmentId) == 0) {
            if ($professorModel->deleteAssignment($assignmentId)) {
                $_SESSION['ok_message'] = 'Assignment deleted.';
            } else {
                $_SESSION['error_message'] = 'Error deleting assignment!';
            }
        } else {
            $_SESSION['error_message'] = 'This assignment can not be deleted because there are tasks added to it!';
        }

        $courseId = $_SESSION['courseId'];
        $professorController = new ProfessorController();
        $professorController->viewAssignments($courseId);
    }

    public function viewTasks($assignmentId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Tasks';

        require('views/common/header.php');

        $assignmentModel = new AssignmentModel();
        $_SESSION['assignmentTitle'] = $assignmentModel->getAssignmentTitle($assignmentId);
        $professorModel = new ProfessorModel();
        $taskModel = new TaskModel();
        $_SESSION['tasks'] = $taskModel->getTasks($assignmentId);

        $tasks = $_SESSION['tasks'];
        $submissions = array();
        foreach ($tasks as $task) {
            $submissions[] = $professorModel->getSubmissionsPerTaskCount($task['id']);
        }

        $_SESSION['submissions'] = $submissions;
        $_SESSION['assignmentId'] = $assignmentId;

        require('views/professor/viewTasks.php');
        require('views/common/footer.php');
    }

    public function addTask($go, $assignmentId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Add Task';

        $_SESSION['assignmentId'] = $assignmentId;

        require('views/common/header.php');

        if ($go) {
            $title = $_SESSION['title'] = $_POST['title'];
            $description = $_SESSION['description'] = $_POST['description'];
            $className = $_SESSION['className'] = $_POST['className'];
            $try = $_SESSION['try'] = $_POST['try'];
            $taskPercentage = $_SESSION['taskPercentage'] = $_POST['taskPercentage'];

            if ($title == '') {
                $_SESSION['error_message'] = 'Type course title!';
                require('views/professor/addTask.php');
                exit();
            } elseif ($description == '') {
                $_SESSION['error_message'] = 'Type course description!';
                require('views/professor/addTask.php');
                exit();
            } elseif ($className == '') {
                $_SESSION['error_message'] = 'Type the name of the class!';
                require('views/professor/addTask.php');
                exit();
            } elseif ($try == '') {
                $_SESSION['error_message'] = 'Type the number of possible submissions!';
                require('views/professor/addTask.php');
                exit();
            } elseif ($taskPercentage == '') {
                $_SESSION['error_message'] = 'Type the task percentage!';
                require('views/professor/addTask.php');
                exit();
            }

            $professorModel = new ProfessorModel();
            $result = $professorModel->insertTask($title, $description, $className, $try, $taskPercentage, $assignmentId);
            if ($result == null) {
                $_SESSION['error_message'] = 'Error inserting task!';
                require('views/professor/addTask.php');
                exit();
            }

            $_SESSION['ok_message'] = 'Task added successfully';
            header('Location: index.php?action=viewTasks&id=' . $assignmentId . '');
        } else {
            require('views/professor/addTask.php');
        }
        require('views/common/footer.php');
    }

    public function editTask($go, $taskId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Edit Task';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $result = $professorModel->getTaskDetails($taskId);

        $oldTitle = '';
        $oldDescription = '';
        $oldClassName = '';
        $oldTry = '';
        $oldTaskPercentage = '';

        if ($result != null) {
            $_SESSION['taskId'] = $taskId;
            $oldTitle = $_SESSION['title'] = $result['title'];
            $oldDescription = $_SESSION['description'] = $result['description'];
            $oldClassName = $_SESSION['className'] = $result['class_name'];
            $oldTry = $_SESSION['try'] = $result['try'];
            $oldTaskPercentage = $_SESSION['taskPercentage'] = $result['grade_percentage'];
        }

        if ($go) {
            $assignmentId = $_SESSION['assignmentId'];
            $taskId = $_SESSION['taskId'] = $_GET['id'];
            $title = $_SESSION['title'] = $_GET['title'];
            $description = $_SESSION['description'] = $_GET['description'];
            $className = $_SESSION['className'] = $_GET['className'];
            $try = $_SESSION['try'] = $_GET['try'];
            $taskPercentage = $_SESSION['taskPercentage'] = $_GET['taskPercentage'];

            if ($title == '') {
                $_SESSION['error_message'] = 'Type course title!';
                require('views/professor/editTask.php');
                exit();
            } elseif ($description == '') {
                $_SESSION['error_message'] = 'Type course description!';
                require('views/professor/editTask.php');
                exit();
            } elseif ($className == '') {
                $_SESSION['error_message'] = 'Type the name of the class!';
                require('views/professor/editTask.php');
                exit();
            } elseif ($try == '') {
                $_SESSION['error_message'] = 'Type the number of possible submissions!';
                require('views/professor/editTask.php');
                exit();
            } elseif ($taskPercentage == '') {
                $_SESSION['error_message'] = 'Type the task percentage!';
                require('views/professor/editTask.php');
                exit();
            }

            if ($title != $oldTitle || $description != $oldDescription || $className != $oldClassName
                || $try != $oldTry || $taskPercentage != $oldTaskPercentage) {
                $professorModel = new ProfessorModel();
                $result = $professorModel->updateTask($taskId, $title, $description, $className, $try, $taskPercentage);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error updating task!';
                    require('views/professor/editTask.php');
                    exit();
                }
            }

            $_SESSION['ok_message'] = 'Task updated successfully';
            header('Location: index.php?action=viewTasks&id=' . $assignmentId . '');
        } else {
            require('views/professor/editTask.php');
        }
        require('views/common/footer.php');
    }

    public function deleteTask($taskId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $professorModel = new ProfessorModel();

        if ($professorModel->getTestsPerTaskCount($taskId) == 0) {
            if ($professorModel->deleteTask($taskId)) {
                $_SESSION['ok_message'] = 'Task deleted.';
            } else {
                $_SESSION['error_message'] = 'Error deleting task!';
            }
        } else {
            $_SESSION['error_message'] = 'This task can not be deleted because there are tests added to it!';
        }

        $assignmentId = $_SESSION['assignmentId'];
        $professorController = new ProfessorController();
        $professorController->viewTasks($assignmentId);
    }

    public function viewTests($taskId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Tests';

        require('views/common/header.php');

        $taskModel = new TaskModel();
        $_SESSION['taskTitle'] = $taskModel->getTaskTitle($taskId);

        $professorModel = new ProfessorModel();
        $_SESSION['tests'] = $professorModel->getTests($taskId);
        $_SESSION['testCodingStyle'] = $professorModel->getTestCodingStyle($taskId);

        $_SESSION['taskId'] = $taskId;
        $_SESSION['runningTestsProfessor'] = $professorModel->getRunningTestsProfessorForTask($taskId);

        require('views/professor/viewTests.php');
        require('views/common/footer.php');
    }

    public function addTest($go, $taskId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Add Test';

        $_SESSION['taskId'] = $taskId;

        require('views/common/header.php');

        if ($go) {
            $description = $_SESSION['description'] = $_POST['description'];
            $inputTest = $_SESSION['inputTest'] = $_POST['inputTest'];
            $outputTest = $_SESSION['outputTest'] = $_POST['outputTest'];
            $grade = $_SESSION['grade'] = $_POST['grade'];
            $visible = $_SESSION['visible'] = $_POST['visible'];

            if ($description == '') {
                $_SESSION['error_message'] = 'Type the description!';
                require('views/professor/addTest.php');
                exit();
            } elseif ($inputTest == '') {
                $_SESSION['error_message'] = 'Type input test!';
                require('views/professor/addTest.php');
                exit();
            } elseif ($outputTest == '') {
                $_SESSION['error_message'] = 'Type output test!';
                require('views/professor/addTest.php');
                exit();
            } elseif ($grade == '') {
                $_SESSION['error_message'] = 'Type grade!';
                require('views/professor/addTest.php');
                exit();
            } elseif ($visible == '') {
                $_SESSION['error_message'] = 'Type visible!';
                require('views/professor/addTest.php');
                exit();
            }

            $professorModel = new ProfessorModel();
            $result = $professorModel->insertTest($description, $inputTest, $outputTest, $grade, $visible, $taskId);
            if ($result == null) {
                $_SESSION['error_message'] = 'Error inserting test!';
                require('views/professor/addTest.php');
                exit();
            }

            $_SESSION['ok_message'] = 'Test added successfully';
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
        } else {
            require('views/professor/addTest.php');
        }
        require('views/common/footer.php');
    }

    public function addTestCodingStyle($go, $taskId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Add test';

        $_SESSION['taskId'] = $taskId;

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $codingStandards = $professorModel->getCodingStandards();
        $_SESSION['codingStandards'] = $codingStandards;

        if ($go) {
            $inputTest = $_SESSION['inputTest'] = $_POST['inputTest'];
            $outputTest = $_SESSION['outputTest'] = $_POST['outputTest'];
            $grade = $_SESSION['grade'] = $_POST['grade'];
            $visible = $_SESSION['visible'] = $_POST['visible'];
            $coding = $_SESSION['coding'] = $_POST['coding'];

            if ($inputTest == '') {
                $_SESSION['error_message'] = 'Type the accepted number of violations!';
                require('views/professor/addTestCodingStyle.php');
                exit();
            } elseif ($outputTest == '') {
                $_SESSION['error_message'] = 'Type the penalty per violation!';
                require('views/professor/addTestCodingStyle.php');
                exit();
            } elseif ($grade == '') {
                $_SESSION['error_message'] = 'Type grade!';
                require('views/professor/addTestCodingStyle.php');
                exit();
            } elseif ($visible == '') {
                $_SESSION['error_message'] = 'Type visible!';
                require('views/professor/addTestCodingStyle.php');
                exit();
            } elseif ($coding == '') {
                $_SESSION['error_message'] = 'Type visible!';
                require('views/professor/addTestCodingStyle.php');
                exit();
            }

            $professorModel = new ProfessorModel();
            $result = $professorModel->insertTestCodingStyle($inputTest, $outputTest, $grade, $visible, $taskId, $coding);
            if ($result == null) {
                $_SESSION['error_message'] = 'Error inserting test!';
                require('views/professor/addTestCodingStyle.php');
                exit();
            }

            $_SESSION['ok_message'] = 'Test added successfully';
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
        } else {
            require('views/professor/addTestCodingStyle.php');
        }
        require('views/common/footer.php');
    }

    public function editTest($go, $testId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Edit Test';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $result = $professorModel->getTestDetails($testId);

        $oldDescription = '';
        $oldInputTest = '';
        $oldOutputTest = '';
        $oldGrade = '';
        $oldVisible = '';

        if ($result != null) {
            $_SESSION['testId'] = $testId;
            $oldDescription = $_SESSION['description'] = $result['description'];
            $oldInputTest = $_SESSION['inputTest'] = $result['input_test'];
            $oldOutputTest = $_SESSION['outputTest'] = $result['output_test'];
            $oldGrade = $_SESSION['grade'] = $result['grade'];
            $oldVisible = $_SESSION['visible'] = $result['type'];
        }

        if ($go) {
            $taskId = $_SESSION['taskId'];
            $testId = $_SESSION['testId'] = $_GET['id'];
            $description = $_SESSION['description'] = $_GET['description'];
            $inputTest = $_SESSION['inputTest'] = $_GET['inputTest'];
            $outputTest = $_SESSION['outputTest'] = $_GET['outputTest'];
            $grade = $_SESSION['grade'] = $_GET['grade'];
            $visible = $_SESSION['visible'] = $_GET['visible'];

            if ($description == '') {
                $_SESSION['error_message'] = 'Type description test!';
                require('views/professor/editTest.php');
                exit();
            } elseif ($inputTest == '') {
                $_SESSION['error_message'] = 'Type input test!';
                require('views/professor/editTest.php');
                exit();
            } elseif ($outputTest == '') {
                $_SESSION['error_message'] = 'Type output test!';
                require('views/professor/editTest.php');
                exit();
            } elseif ($grade == '') {
                $_SESSION['error_message'] = 'Type the grade for the test!';
                require('views/professor/editTest.php');
                exit();
            } elseif ($visible == '') {
                $_SESSION['error_message'] = 'Choose if the test is visible for students!';
                require('views/professor/editTest.php');
                exit();
            }

            if ($oldDescription != $description || $inputTest != $oldInputTest || $outputTest != $oldOutputTest
                || $grade != $oldGrade || $visible != $oldVisible) {
                $professorModel = new ProfessorModel();
                $result = $professorModel->updateTest($testId, $description, $inputTest, $outputTest, $grade, $visible);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error updating test!';
                    require('views/professor/editTest.php');
                    exit();
                }
            }

            $_SESSION['ok_message'] = 'Test updated successfully';
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
        } else {
            require('views/professor/editTest.php');
        }
        require('views/common/footer.php');
    }

    public function editTestCodingStyle($go, $testId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Edit test coding style';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $codingStandards = $professorModel->getCodingStandards();
        $_SESSION['codingStandards'] = $codingStandards;

        $professorModel = new ProfessorModel();
        $result = $professorModel->getTestDetails($testId);

        $oldInputTest = '';
        $oldOutputTest = '';
        $oldGrade = '';
        $oldVisible = '';

        if ($result != null) {
            $_SESSION['testId'] = $testId;
            $oldInputTest = $_SESSION['inputTest'] = $result['input_test'];
            $oldOutputTest = $_SESSION['outputTest'] = $result['output_test'];
            $oldGrade = $_SESSION['grade'] = $result['grade'];
            $oldVisible = $_SESSION['visible'] = $result['type'];
            $oldCoding = $_SESSION['coding'] = $result['coding'];
        }

        if ($go) {
            $taskId = $_SESSION['taskId'];
            $testId = $_SESSION['testId'] = $_GET['id'];
            $inputTest = $_SESSION['inputTest'] = $_GET['inputTest'];
            $outputTest = $_SESSION['outputTest'] = $_GET['outputTest'];
            $grade = $_SESSION['grade'] = $_GET['grade'];
            $visible = $_SESSION['visible'] = $_GET['visible'];
            $coding = $_SESSION['coding'] = $_GET['coding'];

            if ($inputTest == '') {
                $_SESSION['error_message'] = 'Type the accepted number of violations!';
                require('views/professor/editTestCodingStyle.php');
                exit();
            } elseif ($outputTest == '') {
                $_SESSION['error_message'] = 'Type the penalty per violation!';
                require('views/professor/editTestCodingStyle.php');
                exit();
            } elseif ($grade == '') {
                $_SESSION['error_message'] = 'Type the grade for the test!';
                require('views/professor/editTestCodingStyle.php');
                exit();
            } elseif ($visible == '') {
                $_SESSION['error_message'] = 'Choose if the test is visible for students!';
                require('views/professor/editTestCodingStyle.php');
                exit();
            } elseif ($coding == '') {
                $_SESSION['error_message'] = 'Choose the coding standard!';
                require('views/professor/editTestCodingStyle.php');
                exit();
            }

            if ($inputTest != $oldInputTest || $outputTest != $oldOutputTest || $grade != $oldGrade
                || $visible != $oldVisible || $coding != $oldCoding) {
                $professorModel = new ProfessorModel();
                $result = $professorModel->updateTestCodingStyle($testId, $inputTest, $outputTest, $grade, $visible, $coding);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error updating test!';
                    require('views/professor/editTestCodingStyle.php');
                    exit();
                }
            }

            $_SESSION['ok_message'] = 'Test updated successfully';
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
        } else {
            require('views/professor/editTestCodingStyle.php');
        }
        require('views/common/footer.php');
    }

    public function deleteTest($testId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $professorModel = new ProfessorModel();
        $taskId = $_SESSION['taskId'];

        if ($professorModel->getSubmissionsPerTaskCount($taskId) == 0) {
            if ($professorModel->deleteTest($testId)) {
                $_SESSION['ok_message'] = 'Test deleted.';
            } else {
                $_SESSION['error_message'] = 'Error deleting test!';
            }
        } else {
            $_SESSION['error_message'] = 'This test can not be deleted because there are submissions made to this 
                                                task!';
        }

        $taskId = $_SESSION['taskId'];
        $professorController = new ProfessorController();
        $professorController->viewTests($taskId);
    }

    public function deleteTestCodingStyle($testId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $professorModel = new ProfessorModel();
        $taskId = $_SESSION['taskId'];

        if ($professorModel->getSubmissionsPerTaskCount($taskId) == 0) {
            if ($professorModel->deleteTestCodingStyle($testId)) {
                $_SESSION['ok_message'] = 'Test deleted.';
            } else {
                $_SESSION['error_message'] = 'Error deleting test!';
            }
        } else {
            $_SESSION['error_message'] = 'This test can not be deleted because there are submissions made to this 
                                                task!';
        }

        $taskId = $_SESSION['taskId'];
        $professorController = new ProfessorController();
        $professorController->viewTests($taskId);
    }

    public function runTests($taskId, $go)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $professorModel = new ProfessorModel();
        $checkReleaseGrade = $professorModel->checkReleaseAssignmentGrades($_SESSION['assignmentId']);
        if ($checkReleaseGrade) {
            $_SESSION['error_message'] = 'The grades have been released, you can not run tests anymore!';
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
            exit();
        }

        if ($go) {
            $assignmentId = $_SESSION['assignmentId'];

            $professorModel = new ProfessorModel();
            $checkReleaseGrade = $professorModel->checkReleaseAssignmentGrades($assignmentId);

            if ($checkReleaseGrade) {
                $result = $professorModel->setReleaseGradeToFalse($assignmentId);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error running tests!';
                    header('Location: index.php?action=viewTests&id=' . $taskId . '');
                    exit();
                }
            }

            $assignmentId = $_SESSION['assignmentId'];
            $courseId = $_SESSION['courseId'];

            $assignmentModel = new AssignmentModel();
            $assignmentDeadline = $assignmentModel->getAssignmentDeadline($assignmentId);

            $professorModel = new ProfessorModel();
            $studentsDidntSubmitCodePerTask = $professorModel->getStudentsDidntSubmitCodePerTask($taskId, $courseId);

            if (count($studentsDidntSubmitCodePerTask) > 0 && date("Y-m-d H:i:s", time()) > $assignmentDeadline) {
                $result = $professorModel->setFinalGradeStudentsDidntSubmitCode($studentsDidntSubmitCodePerTask, $taskId);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error running tests!';
                    header('Location: index.php?action=viewTests&id=' . $taskId . '');
                    exit();
                }

                $foreignKeysForTaskWithNoSubmittedCode = $professorModel->getForeignKeysForTaskWithNoSubmittedCode($taskId);
                $result1 = $professorModel->setOutputStudentsDidntSubmitCode($foreignKeysForTaskWithNoSubmittedCode);
                if ($result1 == false) {
                    $_SESSION['error_message'] = 'Error running tests!';
                    header('Location: index.php?action=viewTests&id=' . $taskId . '');
                    exit();
                }
            }

            $submissions = $professorModel->getSubmissionsForTaskCount($taskId);
            $enrolledStudents = $professorModel->getEnrolledStudentsCount($courseId);
            if ($enrolledStudents != count($studentsDidntSubmitCodePerTask) && $submissions != 0) {
                $result = $professorModel->runAllTest($taskId, $assignmentId);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error running tests!';
                    header('Location: index.php?action=viewTests&id=' . $taskId . '');
                    exit();
                }
            }

            $_SESSION['ok_message'] = 'Marking server has started running the tests';
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
        } else {
            header('Location: index.php?action=viewTests&id=' . $taskId . '');
        }
    }

    public function runAssignmentTests($assignmentId, $go)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);


        $professorModel = new ProfessorModel();
        $taskIds = $professorModel->getTaskIdsForAssignment($assignmentId);
        $courseId = $professorModel->getCourseId($assignmentId);
        $checkReleaseGrade = $professorModel->checkReleaseAssignmentGrades($assignmentId);

        if (empty($taskIds)) {
            $_SESSION['error_message'] = 'There are no tasks!';
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
            exit();
        }

        if ($checkReleaseGrade) {
            $_SESSION['error_message'] = 'The grades have been released, you can not run tests anymore!';
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
            exit();
        }

        if ($go) {
            if ($checkReleaseGrade) {
                $result = $professorModel->setReleaseGradeToFalse($assignmentId);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error running tests!';
                    header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
                    exit();
                }
            }

            foreach ($taskIds as $taskId) {
                $assignmentModel = new AssignmentModel();
                $assignmentDeadline = $assignmentModel->getAssignmentDeadline($assignmentId);

                $professorModel = new ProfessorModel();
                $studentsDidntSubmitCodePerTask = $professorModel->getStudentsDidntSubmitCodePerTask($taskId['id'], $courseId);

                if (count($studentsDidntSubmitCodePerTask) > 0 && date("Y-m-d H:i:s", time()) > $assignmentDeadline) {
                    $result = $professorModel->setFinalGradeStudentsDidntSubmitCode($studentsDidntSubmitCodePerTask, $taskId['id']);
                    if ($result == false) {
                        $_SESSION['error_message'] = 'Error running tests!';
                        header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
                        exit();
                    }

                    $foreignKeysForTaskWithNoSubmittedCode = $professorModel->getForeignKeysForTaskWithNoSubmittedCode($taskId['id']);
                    $result1 = $professorModel->setOutputStudentsDidntSubmitCode($foreignKeysForTaskWithNoSubmittedCode);
                    if ($result1 == false) {
                        $_SESSION['error_message'] = 'Error running tests!';
                        header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
                        exit();
                    }
                }

                $submissions = $professorModel->getSubmissionsForTaskCount($taskId['id']);
                $enrolledStudents = $professorModel->getEnrolledStudentsCount($courseId);
                if ($enrolledStudents != count($studentsDidntSubmitCodePerTask) && $submissions != 0) {
                    $result = $professorModel->runAllTest($taskId['id'], $assignmentId);
                    if ($result == false) {
                        $_SESSION['error_message'] = 'Error running tests!';
                        header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
                        exit();
                    }
                }

                $_SESSION['ok_message'] = 'Marking server has started running the tests';
                header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
            }
        } else {
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
        }
    }

    public function releaseAssignmentGrade($assignmentId, $go)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        if ($go) {
            $professorModel = new ProfessorModel();
            $result = $professorModel->releaseAssignmentGrade($assignmentId);
            if ($result == false) {
                $_SESSION['error_message'] = 'Error releasing grades!';
                header('Location: index.php?action=professorViewReports&id=' . $assignmentId . '');
                exit();
            }

            $emailer = new Emailer();
            $courseModel = new CourseModel();
            $assignmentModel = new AssignmentModel();

            $courseId = $_SESSION['courseId'];
            $assignmentId = $_SESSION['assignmentId'];

            $courseTitle = $courseModel->getCourseTitle($courseId);
            $assignmentTitle = $assignmentModel->getAssignmentTitle($assignmentId);
            $studentsDetails = $professorModel->getEnrolledStudents($courseId);
            foreach ($studentsDetails as $studentDetails) {
                $emailer->sendEmail($studentDetails['email'], $emailer->releaseGrades($studentDetails['name'], $courseTitle, $assignmentTitle, $assignmentId ));
            }

            $_SESSION['ok_message'] = 'The grades are released.';
            header('Location: index.php?action=professorViewReports&id=' . $assignmentId . '');
        } else {
            header('Location: index.php?action=professorViewReports&id=' . $assignmentId . '');
        }
    }

    public function releaseCourseGrade($courseId, $go)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        if ($go) {
            $professorModel = new ProfessorModel();
            $result = $professorModel->releaseCourseGrade($courseId);
            if ($result == false) {
                $_SESSION['error_message'] = 'Error releasing grades!';
                header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
                exit();
            }

            $_SESSION['ok_message'] = 'The grades are released.';
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
        } else {
            header('Location: index.php?action=viewAssignments&id=' . $courseId . '');
        }
    }

    public function viewAssignmentReport($assignmentId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Assignment Report';

        require('views/common/header.php');

        $assignmentModel = new AssignmentModel();
        $studentModel = new StudentModel();
        $professorModel = new ProfessorModel();

        $navigationDetails = $professorModel->getNavigationDetailsAssignmentReport($assignmentId);
        $_SESSION['courseId'] = $courseId = $navigationDetails[0];
        $_SESSION['courseTitle'] = $courseTitle = $navigationDetails[1];
        $_SESSION['assignmentId'] = $assignmentId = $navigationDetails[2];
        $_SESSION['assignmentTitle'] = $assignmentTitle = $navigationDetails[3];

        $assignmentGradesForStudents = $studentModel->getAssignmentGradesForStudents($assignmentId);
        $taskCount = $professorModel->getTaskCountPerAssignment($assignmentId);
        $taskGradesForStudents = $professorModel->getTaskGradesForStudentsPerAssignment($assignmentId);
        $tasksTitle = $assignmentModel->getTaskTitlePerAssignment($assignmentId);
        $taskMeanGrades = $professorModel->getTaskMeanGrades($assignmentId);
        $checkReleaseAssignmentGrades = $professorModel->checkReleaseAssignmentGrades($assignmentId);

        $gradesIntervalCount = array_fill(0, 11, 0);
        foreach ($assignmentGradesForStudents as $assignmentGradesForStudent) {
            $index = $assignmentGradesForStudent['grade'] / 10 % 10;

            if ($assignmentGradesForStudent['grade'] == 100) {
                $index = 10;
            }
            $gradesIntervalCount[$index]++;
        }

        $gradesTasksIntervalCount = array();
        if (!empty($taskGradesForStudents)) {
            $firstTaskId = $taskGradesForStudents[0]['id'];
            $gradesTask = array_fill(0, 11, 0);
            $gradesTasksIntervalCount = array();
            foreach ($taskGradesForStudents as $taskGradesForStudent) {
                if ($firstTaskId != $taskGradesForStudent['id']) {
                    $gradesTasksIntervalCount[] = array(
                        'grade' => join(',', $gradesTask)
                    );
                    $gradesTask = array_fill(0, 11, 0);
                }

                $index = $taskGradesForStudent['grade'] / 10 % 10;

                if ($taskGradesForStudent['grade'] == 100) {
                    $index = 10;
                }
                $gradesTask[$index]++;

                $firstTaskId = $taskGradesForStudent['id'];
            }

            $gradesTasksIntervalCount[] = array(
                'grade' => join(',', $gradesTask)
            );
        }

        $_SESSION['checkReleaseAssignmentGrades'] = $checkReleaseAssignmentGrades;
        $_SESSION['gradesTasksIntervalCount'] = $gradesTasksIntervalCount;
        $_SESSION['gradesIntervalCount'] = join(',', $gradesIntervalCount);
        $_SESSION['assignmentGradesForStudents'] = $assignmentGradesForStudents;
        $_SESSION['taskGradesForStudents'] = $taskGradesForStudents;
        $_SESSION['assignmentTitle'] = $assignmentTitle;
        $_SESSION['tasksTitle'] = $tasksTitle;
        $_SESSION['taskCount'] = $taskCount;
        $_SESSION['taskMeanGrades'] = $taskMeanGrades;

        require('views/professor/viewAssignmentReport.php');
        require('views/common/footer.php');
    }

    public function viewAssignmentStudentReport($studentId)
    {
        RedirectHelper::redirect(ROLE_PROFESSOR);

        $pageTitle = 'Student Assignment Report';

        require('views/common/header.php');

        $studentModel = new StudentModel();
        $userModel = new UserModel();
        $reports  = $studentModel->getTaskOutput($_SESSION['assignmentId'], $studentId);
        $studentDetails = $userModel->getUserNameSurname($studentId);
        $assignmentSubmissionNo = $studentModel->getAssignmentSubmissionNo($_SESSION['assignmentId'], $studentId);

        $_SESSION['reports'] = $reports;
        $_SESSION['studentDetails'] = $studentDetails;
        $_SESSION['assignmentSubmissionNo'] = $assignmentSubmissionNo;

        require('views/professor/viewAssignmentStudentReport.php');
        require('views/common/footer.php');
    }
}