<?php

require_once('controllers/Emailer.php');
require_once('models/AdministratorModel.php');
require_once('settings/roles.php');

class AdministratorController
{
    public function showUserRequests($page)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $pageTitle = 'Users';

        require('views/common/header.php');

        $filterProfessor = $_SESSION['filterProfessor'] = 'false';
        if (isset($_GET['filterProfessor'])) {
            $filterProfessor = $_SESSION['filterProfessor'] = $_GET['filterProfessor'];
        }

        $filterStudent = $_SESSION['filterStudent'] = 'false';
        if (isset($_GET['filterStudent'])) {
            $filterStudent = $_SESSION['filterStudent'] = $_GET['filterStudent'];
        }

        $filterValidUser = $_SESSION['filterValidUser'] = 'false';
        if (isset($_GET['filterValidUser'])) {
            $filterValidUser = $_SESSION['filterValidUser'] = $_GET['filterValidUser'];
        }

        $filterInvalidUser = $_SESSION['filterInvalidUser'] = 'false';
        if (isset($_GET['filterInvalidUser'])) {
            $filterInvalidUser = $_SESSION['filterInvalidUser'] = $_GET['filterInvalidUser'];
        }

        $administratorModel = new AdministratorModel();
        $rows = $administratorModel->getUserRequestsCount($filterProfessor, $filterStudent, $filterValidUser,
            $filterInvalidUser);

        $resultsPerPage = 5;
        $_SESSION['numberOfPages'] = ceil($rows / $resultsPerPage);
        $pageFirstResult = ($page - 1) * $resultsPerPage;
        $_SESSION['users'] = $administratorModel->getUserRequests($pageFirstResult, $resultsPerPage, $filterProfessor,
            $filterStudent, $filterValidUser, $filterInvalidUser);

        $uri = $_SERVER['REQUEST_URI'];
        $_SESSION['uri'] = $uri;

        require('views/administrator/administrator.php');
        require('views/common/footer.php');
    }

    public function viewCourses()
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $pageTitle = 'View all courses';

        $administratorModel = new AdministratorModel();
        require('views/common/header.php');

        $_SESSION['courses'] = $administratorModel->getCourses();

        require('views/administrator/viewCourses.php');
        require('views/common/footer.php');
    }

    public function editUserRequest($go, $id)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $pageTitle = 'Edit user';

        require('views/common/header.php');

        $administratorModel = new AdministratorModel();
        $result = $administratorModel->getUserAccountDetails($id);

        $oldName = '';
        $oldSurname = '';
        $oldEndDate = '';
        $oldRole = '';

        if ($result != null) {
            $_SESSION['id'] = $id;
            $oldName = $_SESSION['name'] = $result['name'];
            $oldSurname = $_SESSION['surname'] = $result['surname'];
            $oldEndDate = $_SESSION['endDate'] = $result['end_date'];
            $oldRole = $_SESSION['roleIdUserRequest'] = $result['role_id'];
        }

        if ($go) {
            $userId = $_SESSION['id'] = $_GET['id'];
            $name = $_SESSION['name'] = $_GET['name'];
            $surname = $_SESSION['surname'] = $_GET['surname'];
            $endDate = $_SESSION['endDate'] = $_GET['endDate'];
            $role = $_SESSION['roleIdUserRequest'] = $_GET['role'];

            $roleModel = new RoleModel();
            $_SESSION['roles'] = $roleModel->getRoles();

            if ($name == '') {
                $_SESSION['error_message'] = 'Type your name!';
                require('views/administrator/editUser.php');
                exit();
            } elseif (!preg_match("/^([a-zA-Z' ]+)$/", $name)) {
                $_SESSION['error_message'] = 'Type a valid name!';
                require('views/administrator/editUser.php');
                exit();
            } elseif ($surname == '') {
                $_SESSION['error_message'] = 'Type your surname!';
                require('views/administrator/editUser.php');
                exit();
            } elseif (!preg_match("/^([a-zA-Z' ]+)$/", $surname)) {
                $_SESSION['error_message'] = 'Type a valid surname!';
                require('views/administrator/editUser.php');
                exit();
            } elseif ($endDate == '') {
                $_SESSION['error_message'] = 'Select end date';
                require('views/administrator/editUser.php');
                exit();
            } elseif ($role == 0) {
                $_SESSION['error_message'] = 'Select a role!';
                require('views/administrator/editUser.php');
                exit();
            }

            if ($name != $oldName || $surname != $oldSurname || $endDate != $oldEndDate || $role != $oldRole) {
                $administratorModel = new AdministratorModel();
                $result = $administratorModel->updateUser($userId, $name, $surname, $endDate, $role);
                if ($result == false) {
                    $_SESSION['error_message'] = 'Error updating person!';
                    require('views/administrator/editUser.php');
                    exit();
                }
            }

            if ($_SESSION['userId'] == $userId) {
                $_SESSION['userName'] = $name;
                $_SESSION['userSurname'] = $surname;
            }

            if ($_SESSION['userId'] == $userId && $role != ROLE_ADMINISTRATOR) {
                unset($_SESSION['userId']);
                unset($_SESSION['userName']);
                unset($_SESSION['userSurname']);
                unset($_SESSION['roleId']);
                $_SESSION['ok_message'] = 'You logged out.';
                header('Location: index.php?action=login');
                exit();
            }

            $_SESSION['ok_message'] = 'Account details were updated.';
            header('Location: index.php?action=administrator');
        } else {
            $roleModel = new RoleModel();
            $_SESSION['roles'] = $roleModel->getRoles();
            require('views/administrator/editUser.php');
        }
        require('views/common/footer.php');
    }

    public function deleteUserRequests($id)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $administratorModel = new AdministratorModel();

        if ($administratorModel->deleteUserRequest($id)) {
            $_SESSION['ok_message'] = 'User deleted';
        } else {
            $_SESSION['error_message'] = 'Error deleting user';
        }

        if ($_SESSION['userId'] == $id) {
            unset($_SESSION['userId']);
            unset($_SESSION['userName']);
            unset($_SESSION['userSurname']);
            unset($_SESSION['roleId']);
            $_SESSION['ok_message'] = 'You logged out.';
            header('Location: index.php?action=login');
        }

        $uri = $_SESSION['uri'];
        header('Location: ' . $uri);
    }

    public function validateUserRequests($id)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $administratorModel = new AdministratorModel();
        $userModel = new UserModel();

        if ($administratorModel->validateUserRequest($id)) {
            $studentEmail = $userModel->getUserEmail($id);
            $studentName = $userModel->getUSerName($id);
            $role = $userModel->getUserRole($id);

            $emailer = new Emailer();
            $emailer->sendEmail($studentEmail, $emailer->requestAccountDecision($studentName, $role,'accepted'));;

            $_SESSION['ok_message'] = 'User validated.';
        } else {
            $_SESSION['error_message'] = 'Error validating user.';
        }

        $uri = $_SESSION['uri'];
        header('Location: ' . $uri);
    }

    public function invalidateUser($id)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $administratorModel = new AdministratorModel();
        $userModel = new UserModel();

        if ($administratorModel->invalidateUser($id)) {
            $studentEmail = $userModel->getUserEmail($id);
            $studentName = $userModel->getUSerName($id);
            $role = $userModel->getUserRole($id);

            $emailer = new Emailer();
            $emailer->sendEmail($studentEmail, $emailer->requestAccountDecision($studentName, $role,'declined'));;

            $_SESSION['ok_message'] = 'User invalidated.';
        } else {
            $_SESSION['error_message'] = 'Error invalidating user!';
        }

        $uri = $_SESSION['uri'];
        header('Location: ' . $uri);
    }

    public function bulkEnrollment()
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $pageTitle = 'Bulk enrollment';

        require('views/common/header.php');

        $path = getcwd() . '/bulkEnrollment/';
        if (!empty($_FILES)) {
            $fileName = $_FILES["fileToUpload"]["name"];
            $fileType = $_FILES["fileToUpload"]["type"];
            $fileSize = $_FILES["fileToUpload"]["size"];
            $tempFile = $_FILES["fileToUpload"]["tmp_name"];

            $fileExtension = '.csv';
            if ($fileType != "application/vnd.ms-excel" ||
                substr_compare($fileName, $fileExtension, -strlen($fileExtension)) !== 0) {
                $_SESSION['error_message'] = 'Please upload a csv file with extension "' . $fileExtension . '"!';
                header('Location: index.php?action=bulkEnrollment');
                exit();
            }

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $fileNameWithDirectory = $path . '/' . $fileName;

            if (move_uploaded_file($tempFile, $fileNameWithDirectory)) {
                $_SESSION['ok_message'] = 'File ' . $fileName . ' of size ' . $fileSize . ' bytes has been uploaded.';
            } else {
                $_SESSION['error_message'] = 'Error occurred during file upload!';
            }
            header('Location: index.php?action=bulkEnrollment');

            if (($handle = fopen($path . '/' . $fileName, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $user[] = array(
                        'email'    => $data[0],
                        'password' => sha1($data[1]),
                        'clearPassword' => $data[1],
                        'name'     => $data[2],
                        'surname'  => $data[3],
                        'endDate'  => $data[4],
                        'role'     => $data[5]
                    );
                }
                fclose($handle);

                if (isset($user)) {
                    $administratorModel = new AdministratorModel();
                    $result = $administratorModel->bulkEnrollment($user);

                    if ($result != null) {
                        $emailer = new Emailer();

                        foreach ($user as $u) {
                            $emailer->sendEmail($u['email'], $emailer->bulkEnrollment($u['name'], $u['clearPassword']));
                        }

                        $_SESSION['ok_message'] = $result . ' users were inserted.';
                        header('Location: index.php?action=bulkEnrollment');
                    } else {
                        $_SESSION['error_message'] = 'Error occurred during inserting users!';
                    }
                }
            }
        }
        require('views/administrator/bulkEnrollment.php');
        require('views/common/footer.php');
    }

    public function viewCourseStudents($courseId)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $pageTitle = 'Enrolled students';

        require('views/common/header.php');

        $professorModel = new ProfessorModel();
        $courseModel = new CourseModel();
        $_SESSION['enrolledStudents'] = $professorModel->getEnrolledStudents($courseId);
        $_SESSION['studentCount'] = $professorModel->getEnrolledStudentsCount($courseId);
        $_SESSION['courseId'] = $courseId;
        $_SESSION['courseTitle'] = $courseModel->getCourseTitle($courseId);

        require('views/administrator/viewEnrolledStudents.php');
        require('views/common/footer.php');
    }

    public function removeUserFromCourse($studentId)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $courseId = $_SESSION['courseId'];
        $administratorModel = new AdministratorModel();

        if ($administratorModel->removeUserFromCourse($studentId, $courseId)) {
            $_SESSION['ok_message'] = 'User removed from course.';
        } else {
            $_SESSION['error_message'] = 'Error removing user.';
        }

        $administratorController = new AdministratorController();
        $administratorController->viewCourseStudents($courseId);
    }

    public function addStudentsToCourse($courseId)
    {
        RedirectHelper::redirect(ROLE_ADMINISTRATOR);

        $pageTitle = 'Add students to course';

        require('views/common/header.php');

        $administratorModel = new AdministratorModel();
        $courseModel = new CourseModel();
        $_SESSION['users'] = $administratorModel->getUsersNotInCourse($courseId);
        $_SESSION['courseId'] = $courseId;
        $_SESSION['courseTitle'] = $courseModel->getCourseTitle($courseId);

        require('views/administrator/addStudentToCourse.php');
        require('views/common/footer.php');
    }

    public function addStudentToCourse($userId, $courseId)
    {
        $administratorModel = new AdministratorModel();

        if ($administratorModel->addUserToCourse($userId, $courseId)) {
            $_SESSION['ok_message'] = 'User added successfully.';
        } else {
            $_SESSION['error_message'] = 'Error adding user.';
        }

        $administratorController = new AdministratorController();
        $administratorController->addStudentsToCourse($courseId);
    }
}