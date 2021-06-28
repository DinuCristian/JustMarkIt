<script>
    function clearFields(){
        clearMessage();

        clearLabel('passwordLabel');
        clearLabel('confirmPasswordLabel');
    }

    function validate() {
        var password = document.forms['form']['password'];
        var confirmPassword = document.forms['form']['confirmPassword'];
        elements = [password, confirmPassword];

        clearFields();
        elements.forEach(function (item, index) {
            item.addEventListener("invalid", clearFields, false);
        })

        result = requiredField('passwordLabel', password, 'password');
        if (!result) {
            return false;
        }

        result = requiredField('confirmPasswordLabel', confirmPassword, 'confirm password');
        if (!result) {
            return false;
        }

        result = comparePasswords('confirmPasswordLabel', password, confirmPassword);
        if (!result) {
            return false;
        }

        return true;
    }
</script>

<?php

$userId = 0;
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
}

$roleId = '';
if (isset($_SESSION['roleId'])) {
    $roleId = $_SESSION['roleId'];
}

function role($role) {
    if ($role == 1) {
        return 'administrator';
    } elseif ($role == 2) {
        return 'professor';
    } elseif ($role ==3) {
        return 'student';
    } else {
        return 'da';
    }
}

echo '<div id="error"><p id="message" style="color: red;"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table class="addCourse-table">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=editAccountDetails&id=' . $userId . '&go=true" method="post">';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="action" value="editAccountDetails" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $userId . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="passwordLabel">New Password*</label></td>';
    echo '         <td><input type="password" id="password" name="password" size="25" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="confirmPasswordLabel">Confirm New Password*</label></td>';
    echo '         <td><input type="password" id="confirmPassword" name="confirmPassword" size="25" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

    echo '<br>';
    echo '<a href="index.php?action=' . role($roleId) . '">Back to main page</a>';
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}
