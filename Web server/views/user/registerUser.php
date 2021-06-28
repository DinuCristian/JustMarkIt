<script>
    function clearFields(){
        clearMessage();

        clearLabel('emailLabel');
        clearLabel('nameLabel');
        clearLabel('surnameLabel');
        clearLabel('passwordLabel');
        clearLabel('password2Label');
        clearLabel('endDateLabel');
        clearLabel('roleLabel');
    }

    function validate() {
        var email = document.forms['form']['email'];
        var name = document.forms['form']['name'];
        var surname = document.forms['form']['surname'];
        var password = document.forms['form']['password'];
        var password2 = document.forms['form']['password2'];
        var endDate = document.forms['form']['endDate'];
        var role = document.forms['form']['role'];
        elements = [email, name, surname, password, password2, endDate, role];

        clearFields();
        elements.forEach(function (item, index) {
            item.addEventListener("invalid", clearFields, false);
        })

        result = requiredField('emailLabel', email, 'email');
        if (!result) {
            return false;
        }

        result = validEmail('emailLabel', email);
        if (!result) {
            return false;
        }

        result = requiredField('nameLabel' ,name, 'name');
        if (!result) {
            return false;
        }

        result = validField('nameLabel', name, 'name');
        if (!result) {
            return false;
        }

        result = requiredField('surnameLabel', surname, 'surname');
        if (!result) {
            return false;
        }

        result = validField('surnameLabel', surname, 'surname');
        if (!result) {
            return false;
        }

        result = requiredField('passwordLabel', password, 'password');
        if (!result) {
            return false;
        }

        result = comparePasswords('password2Label', password, password2);
        if (!result) {
            return false;
        }

        result = requiredField('endDateLabel', endDate, 'end date');
        if (!result) {
            return false;
        }

        result = validDate('endDateLabel', endDate);
        if (!result) {
            return false;
        }

        result = requiredField('roleLabel', role, 'role');
        if (!result) {
            return false;
        }

        return true;
    }
</script>

<?php

$email = '';
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    unset($_SESSION['email']);
}

$name = '';
if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
    unset($_SESSION['name']);
}

$surname = '';
if (isset($_SESSION['surname'])) {
    $surname = $_SESSION['surname'];
    unset($_SESSION['surname']);
}

$endDate = '';
if (isset($_SESSION['endDate'])) {
    $endDate = $_SESSION['endDate'];
    unset($_SESSION['endDate']);
}

$roleId = '';
if (isset($_SESSION['roleId'])) {
    $roleId = $_SESSION['roleId'];
    unset($_SESSION['roleId']);
}

$roles = '';
if (isset($_SESSION['roles'])) {
    $roles = $_SESSION['roles'];
    unset($_SESSION['roles']);
}

echo '<h1 style="text-align: center">Register</h1>';

echo '<div id="error"><p id="message" style="color: red; text-align: center"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red; text-align: center">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table style="margin-left: auto; margin-right: auto;">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=register&go=true" method="post">';
    echo '        <tr>';
    echo '            <td><label id="emailLabel">Email*</label></td>';
    echo '         <td><input type="email" id="email" name="email" value="' . $email . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="nameLabel">Name*</label></td>';
    echo '            <td><input type="text" id="name" name="name" value="' . $name . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="surnameLabel">Surname*</label></td>';
    echo '            <td><input type="text" id="surname" name="surname" value="' . $surname . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="passwordLabel">Password*</label></td>';
    echo '            <td><input type="password" id="password" name="password1" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="password2Label">Confirm Password*</label></td>';
    echo '            <td><input type="password" id="password2" name="password2" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="endDateLabel">End Date*</label></td>';
    echo '            <td><input type="date" id="endDate" name="endDate" value="' . $endDate . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="roleLabel">Role*</label></td>';
    echo '            <td>';
    echo '                <select id="role" name="role" required>';
    echo '                <option value="">Select role</option>';
                            foreach ($roles as $role) {
                                echo '<option value="' . $role['id'] . '">' . $role['role'] . '</option>';
                            }
    echo '                </select>';
    echo '            </td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

    echo '<br>';
    echo '<div style="text-align: center">';
    echo '    <a href="index.php?action=login">Login</a>';
    echo '</div>';
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green; text-align: center">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}
