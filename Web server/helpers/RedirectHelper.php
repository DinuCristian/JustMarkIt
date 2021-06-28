<?php


class RedirectHelper
{
    public function redirect($roleId)
    {
        if (!isset($_SESSION['roleId'])) {
            $_SESSION['request'] = parse_url($_SERVER['REQUEST_URI'])['query'];
            header('Location: index.php');
            exit();
        }

        if (!isset($_SESSION['roleId']) || $_SESSION['roleId'] != $roleId) {
            require('views/unauthorizedAccess.html');
            exit();
        }
    }
}