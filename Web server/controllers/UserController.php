<?php

require_once('controllers/Emailer.php');
require_once('models/UserModel.php');
require_once('models/RoleModel.php');
require_once('settings/roles.php');
require_once('settings/db.php');

class UserController
{
    public function showLogin($go)
    {
        if (isset($_SESSION['userId'])) {
            $_SESSION['error_message'] = 'You are already logged in!';
            header('Location: index.php');
            exit();
        }

        require('views/common/welcomeHeader.php');

        $roleModel = new RoleModel();
        $_SESSION['roles'] = $roleModel->getRoles();

        if ($go) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $roleId = $_POST['role'];


            if ($email == '') {
                $_SESSION['error_message'] = 'Type your email!';
                require('views/user/login.php');
                exit();
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = 'Invalid email format!';
                require('views/user/login.php');
                exit();
            } elseif ($password == '') {
                $_SESSION['error_message'] = 'Type your password!';
                require('views/user/login.php');
                exit();
            } elseif ($roleId == 0) {
                $_SESSION['error_message'] = 'Select a role!';
                require('views/user/login.php');
                exit();
            }

            // Hash password
            $password = sha1($password);

            $userModel = new UserModel();
            $validUser = $userModel->checkUserExists($email, $password, $roleId);

            if ($validUser == null) {
                $_SESSION['error_message'] = 'Incorrect email and/or password!';
                require('views/user/login.php');
                exit();
            } else {
                $result = $userModel->getUserInfo($email, $password, $roleId);
                $_SESSION['userId'] = $result['id'];
                $_SESSION['userName'] = $result['name'];
                $_SESSION['userSurname'] = $result['surname'];
                $_SESSION['roleId'] = $roleId;
                $_SESSION['ok_message'] = 'You logged in.';

                if (isset($_SESSION['request']) && ($roleId == ROLE_ADMINISTRATOR || $roleId == ROLE_PROFESSOR || $roleId == ROLE_STUDENT)) {
                    $request = $_SESSION['request'];
                    unset($_SESSION['request']);
                    header('Location: index.php?' . $request);
                    exit();
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
            }
        } else {
            include('views/user/login.php');
        }
        require('views/common/footer.php');
    }

    public function showLogout()
    {
        unset($_SESSION['userId']);
        unset($_SESSION['userName']);
        unset($_SESSION['userSurname']);
        unset($_SESSION['roleId']);
        $_SESSION['ok_message'] = 'You logged out.';
        header('Location: index.php?action=login');
    }

    public function showRegister($go)
    {
        if (isset($_SESSION['userId'])) {
            $_SESSION['error_message'] = 'You are logged in. You cannot register while logged in!';
            exit();
        }

        require('views/common/welcomeHeader.php');

        if ($go) {
            $email = $_SESSION['email'] = $_POST['email'];
            $name = $_SESSION['name'] = $_POST['name'];
            $surname = $_SESSION['surname'] = $_POST['surname'];
            $password1 = $_SESSION['password1'] = $_POST['password1'];
            $password2 = $_SESSION['password2'] = $_POST['password2'];
            $endDate = $_SESSION['endDate'] = $_POST['endDate'];
            $roleId = $_SESSION['roleId'] = $_POST['role'];

            $roleModel = new RoleModel();
            $userModel = new UserModel();
            $_SESSION['roles'] = $roleModel->getRoles();

            $result = $userModel->userTaken($email, $roleId);
            if ($result) {
                $_SESSION['error_message'] = 'A user with requested email and role already exists!';
                require('views/user/registerUser.php');
                exit();
            }

            if ($email == '') {
                $_SESSION['error_message'] = 'Type your email!';
                require('views/user/registerUser.php');
                exit();
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = 'Invalid email format!';
                require('views/user/registerUser.php');
                exit();
            } elseif ($name == '') {
                $_SESSION['error_message'] = 'Type your name!';
                require('views/user/registerUser.php');
                exit();
            } elseif (!preg_match("/^([a-zA-Z' ]+)$/", $name)) {
                $_SESSION['error_message'] = 'Type a valid name!';
                require('views/user/registerUser.php');
                exit();
            } elseif ($surname == '') {
                $_SESSION['error_message'] = 'Type your surname!';
                require('views/user/registerUser.php');
                exit();
            } elseif (!preg_match("/^([a-zA-Z' ]+)$/", $surname)) {
                $_SESSION['error_message'] = 'Type a valid surname!';
                require('views/user/registerUser.php');
                exit();
            } elseif ($password1 == '') {
                $_SESSION['error_message'] = 'Type your password!';
                require('views/user/registerUser.php');
                exit();
            } elseif (($password1 != '*****' || $password2 != '#####') && ($password2 != $password1)) {
                $_SESSION['error_message'] = 'Passwords don\'t match!';
                require('views/user/registerUser.php');
                exit();
            } elseif ($endDate == '') {
                $_SESSION['error_message'] = 'Select end date!';
                require('views/user/registerUser.php');
                exit();
            } elseif ($roleId == '') {
                $_SESSION['error_message'] = 'Select a role!';
                require('views/user/registerUser.php');
                exit();
            }

            // Hash password
            $password = sha1($password1);

            $userId = $userModel->requestUserAccount($email, $password, $name, $surname, $endDate, $roleId);
            if ($userId == null) {
                $_SESSION['error_message'] = 'Error creating person!';
                require('views/user/registerUser.php');
                exit();
            }

            $emailer = new Emailer();
            $roleModel = new RoleModel();

            $role = $roleModel->getRole($roleId);
            $emailer->sendEmail($email, $emailer->requestAccountParameters($name, $role));

            if ($roleId == 1) {
                $emailer->sendEmail(MASTER_EMAIL, $emailer->notifyAdmin($name));
            }

            $_SESSION['ok_message'] = 'Your registration has been submitted and it will be processed shortly.';
            header('Location: index.php?action=login');
        } else {
            $roleModel = new RoleModel();
            $_SESSION['roles'] = $roleModel->getRoles();
            require('views/user/registerUser.php');
        }
        require('views/common/footer.php');
    }

    public function editAccountDetails($go, $userId)
    {
        if (!isset($_SESSION['roleId'])) {
            $_SESSION['request'] = parse_url($_SERVER['REQUEST_URI'])['query'];
            header('Location: index.php');
            exit();
        }

        $pageTitle = 'Change password';

        require('views/common/header.php');

        if ($go) {
            $password1 = $_SESSION['password1'] = $_POST['password'];
            $password2 = $_SESSION['password2'] = $_POST['confirmPassword'];

            if ($password1 == '') {
                $_SESSION['error_message'] = 'Type the password!';
                require('views/user/editAccountDetails.php');
                exit();
            } elseif ($password2 == '') {
                $_SESSION['error_message'] = 'Confirm the password!';
                require('views/user/editAccountDetails.php');
                exit();
            } elseif ($password1 != $password2) {
                $_SESSION['error_message'] = 'The two passwords should match!';
                require('views/user/editAccountDetails.php');
                exit();
            }

            // Hash password
            $password = sha1($password1);

            $userModel = new UserModel();
            $result = $userModel->changePassword($userId, $password);
            var_dump($result);
            if ($result == null) {
                $_SESSION['error_message'] = 'Error changing password!';
                require('views/user/editAccountDetails.php');
                exit();
            }

            $_SESSION['ok_message'] = 'Your password has been changed.';
            header('Location: index.php?action=editAccountDetails');
        } else {
            require('views/user/editAccountDetails.php');
        }
        require('views/common/footer.php');
    }
}