<!DOCTYPE html>

    <head>
        <title><?php echo $pageTitle; ?></title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script src="js/validation.js"></script>
    </head>

    <body>
        <div id="page-container">
            <div id="content-wrap">

                <table id="header-table">
                    <tr>
                        <td width="45px">
                            <?php
                            require_once('settings/roles.php');

                            $roleId = ROLE_UNKNOWN;
                            if (isset($_SESSION['roleId'])) {
                                $roleId = $_SESSION['roleId'];
                            }

                            if ($roleId == ROLE_ADMINISTRATOR) {
                                echo '<a href="index.php?action=administrator">';
                            } else if ($roleId == ROLE_PROFESSOR) {
                                echo '<a href="index.php?action=professor">';
                            } else if ($roleId == ROLE_STUDENT) {
                                echo '<a href="index.php?action=student">';
                            }

                            echo '<img src="images/logo.png" style="width:40; height:40;">';

                            if ($roleId == ROLE_ADMINISTRATOR || $roleId == ROLE_PROFESSOR || $roleId == ROLE_STUDENT) {
                                echo '</a>';
                            }
                            ?>
                        </td>
                        <td style="vertical-align: center">
                            <div style="font-size: xx-large"><b><?php echo $pageTitle; ?></b></div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: right">
                            <?php
                            if (isset($_SESSION['userId']) && isset($_SESSION['userName']) && isset($_SESSION['userSurname'])) {
                                echo '<a href="index.php?action=editAccountDetails">' . $_SESSION['userName'] . ' ' . $_SESSION['userSurname'] . '</a>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: right">
                            <?php
                            if (isset($_SESSION['userId'])) {
                                echo '<a href="index.php?action=logout">Logout</a>';
                            } else {
                                echo '<a href="index.php?action=login">Login</a>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center;">
                            <?php
                            if (isset($_SESSION['error_message'])) {
                                echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
                                unset($_SESSION['error_message']);
                            } elseif (isset($_SESSION['ok_message'])) {
                                echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
                                unset($_SESSION['ok_message']);
                            }
                            ?>
                        </td>
                    </tr>
                </table>

                <br>