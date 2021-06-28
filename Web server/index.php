<?php

session_start();

require_once('controllers/UserController.php');
require_once('controllers/AdministratorController.php');
require_once('controllers/ProfessorController.php');
require_once('controllers/StudentController.php');
require_once('controllers/Emailer.php');
require_once('settings/roles.php');

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action != 'downloadFile') {
    require('views/common/logo.html');
}

switch ($action) {
    case 'login':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $userController = new UserController();
        $userController->showLogin($go);
        break;
    case 'logout':
        $userController = new UserController();
        $userController->showLogout();
        break;
    case 'register':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $userController = new UserController();
        $userController->showRegister($go);
        break;
    case 'editAccountDetails':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $userId = 0;
        if (isset($_GET['id'])) {
            $userId = $_GET['id'];
        }
        $userController = new UserController();
        $userController->editAccountDetails($go, $userId);
        break;
    case 'administrator':
        $page = 1;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        $administratorController = new AdministratorController();
        $administratorController->showUserRequests($page);
        break;
    case 'viewCourses':
        $administratorController = new AdministratorController();
        $administratorController->viewCourses();
        break;
    case 'bulkEnrollment':
        $administratorController = new AdministratorController();
        $administratorController->bulkEnrollment();
        break;
    case 'professor':
        $professorController = new ProfessorController();
        $professorController->viewCourses();
        break;
    case 'addCourse':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $professorController = new ProfessorController();
        $professorController->addCourse($go);
        break;
    case 'editCourse':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->editCourse($go, $id);
        break;
    case 'deleteCourse':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->deleteCourse($courseId);
        break;
    case 'viewEnrolledStudents':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->viewEnrolledStudents($courseId);
        break;
    case 'viewAssignments':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->viewAssignments($courseId);
        break;
    case 'addAssignment':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->addAssignment($go, $courseId);
        break;
    case 'editAssignment':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->editAssignment($go, $assignmentId);
        break;
    case 'deleteAssignment':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->deleteAssignment($assignmentId);
        break;
    case 'deleteUser':
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->deleteUserRequests($id);
        break;
    case 'validateUser':
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->validateUserRequests($id);
        break;
    case 'invalidateUser':
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->invalidateUser($id);
        break;
    case 'editUser':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->editUserRequest($go, $id);
        break;
    case 'viewTasks':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->viewTasks($assignmentId);
        break;
    case 'addTask':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->addTask($go, $assignmentId);
        break;
    case 'editTask':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->editTask($go, $taskId);
        break;
    case 'deleteTask':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->deleteTask($taskId);
        break;
    case 'releaseAssignmentGrade':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $professorController = new ProfessorController();
        $professorController->releaseAssignmentGrade($assignmentId, $go);
        break;
    case 'releaseCourseGrade':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $professorController = new ProfessorController();
        $professorController->releaseCourseGrade($courseId, $go);
        break;
    case 'viewTests':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->viewTests($taskId);
        break;
    case 'addTest':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->addTest($go, $taskId);
        break;
    case 'addTestCodingStyle':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->addTestCodingStyle($go, $taskId);
        break;
    case 'editTest':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $testId = 0;
        if (isset($_GET['id'])) {
            $testId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->editTest($go, $testId);
        break;
    case 'editTestCodingStyle':
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $testId = 0;
        if (isset($_GET['id'])) {
            $testId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->editTestCodingStyle($go, $testId);
        break;
    case 'deleteTest':
        $testId = 0;
        if (isset($_GET['id'])) {
            $testId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->deleteTest($testId);
        break;
    case 'deleteTestCodingStyle':
        $testId = 0;
        if (isset($_GET['id'])) {
            $testId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->deleteTestCodingStyle($testId);
        break;
    case 'student':
        $studentController = new StudentController();
        $studentController->viewCourses();
        break;
    case 'studentAssignments':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->viewAssignments($courseId);
        break;
    case 'studentTasks':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->viewTasks($assignmentId);
        break;
    case 'studentTests':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->viewTests($taskId);
        break;
    case 'addCode':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->addCode($taskId);
        break;
    case 'viewCode':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->viewCode($taskId);
        break;
    case 'downloadFile':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $version = 0;
        if (isset($_GET['version'])) {
            $version = $_GET['version'];
        }
        $final = 0;
        if (isset($_GET['final'])) {
            $final = $_GET['final'];
        }
        $studentController = new StudentController();
        $studentController->downloadFile($taskId, $version, $final);
        break;
    case 'runTests':
        $taskId = 0;
        if (isset($_GET['id'])) {
            $taskId = $_GET['id'];
        }
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $professorController = new ProfessorController();
        $professorController->runTests($taskId, $go);
        break;
    case 'runAssignmentTests':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $go = false;
        if (isset($_GET['go'])) {
            $go = $_GET['go'];
        }
        $professorController = new ProfessorController();
        $professorController->runAssignmentTests($assignmentId, $go);
        break;
    case 'setFinal':
        $submissionId = 0;
        if (isset($_GET['id'])) {
            $submissionId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->setFinal($submissionId);
        break;
    case 'viewTestOutput':
        $submissionId = 0;
        if (isset($_GET['id'])) {
            $submissionId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->viewTestOutput($submissionId);
        break;
    case 'studentViewReports':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $studentController = new StudentController();
        $studentController->viewAssignmentReport($assignmentId);
        break;
    case 'professorViewReports':
        $assignmentId = 0;
        if (isset($_GET['id'])) {
            $assignmentId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->viewAssignmentReport($assignmentId);
        break;
    case 'professorViewStudentReports':
        $userId = 0;
        if (isset($_GET['id'])) {
            $userId = $_GET['id'];
        }
        $professorController = new ProfessorController();
        $professorController->viewAssignmentStudentReport($userId);
        break;
    case 'adminViewCourseStudents':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->viewCourseStudents($courseId);
        break;
    case 'adminAddStudentsToCourse':
        $courseId = 0;
        if (isset($_GET['id'])) {
            $courseId = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->addStudentsToCourse($courseId);
        break;
    case 'removeUserFromCourse':
        $studentId = 0;
        if (isset($_GET['id'])) {
            $studentId = $_GET['id'];
        }
        $administratorController = new AdministratorController();
        $administratorController->removeUserFromCourse($studentId);
        break;
    case 'adminAddStudentToCourse':
        $userId = 0;
        if (isset($_GET['userId'])) {
            $userId = $_GET['userId'];
        }
        $courseId = 0;
        if (isset($_GET['courseId'])) {
            $courseId = $_GET['courseId'];
        }
        $administratorController = new AdministratorController();
        $administratorController->addStudentToCourse($userId, $courseId);
        break;
    default:
        $roleId = ROLE_UNKNOWN;
        if (isset($_SESSION['roleId'])) {
            $roleId = $_SESSION['roleId'];
        }

        if ($roleId == ROLE_ADMINISTRATOR) {
            header('Location: index.php?action=administrator');
            exit();
        } else if ($roleId == ROLE_PROFESSOR) {
            header('Location: index.php?action=professor');
            exit();
        } else if ($roleId == ROLE_STUDENT) {
            header('Location: index.php?action=student');
            exit();
        }

        $userController = new UserController();
        $userController->showLogin('');
        break;
}