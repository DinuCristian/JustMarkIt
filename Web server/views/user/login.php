<script>
    function clearFields(){
        clearMessage();

        clearLabel('emailLabel');
        clearLabel('passwordLabel');
        clearLabel('roleLabel');
    }

    function validate() {
        var email = document.forms['form']['email'];
        var password = document.forms['form']['password'];
        var role = document.forms['form']['role'];
        elements = [email, password, role];

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

        result = requiredField('passwordLabel', password, 'password');
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

$roles = '';
if (isset($_SESSION['roles'])) {
    $roles = $_SESSION['roles'];
    unset($_SESSION['roles']);
}

echo '<h1 style="text-align: center">Login</h1>';

echo '<div id="error"><p id="message" style="color: red; text-align: center"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red; text-align: center">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green; text-align: center">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}

echo '<table style="margin-left: auto; margin-right: auto;">';
echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=login&go=true" method="post">';
echo '        <tr>';
echo '            <td><label id="emailLabel">Email</label></td>';
echo '         <td><input type="email" id="email" name="email" size="35" required></td>';
echo '        </tr>';
echo '        <tr>';
echo '            <td><label id="passwordLabel">Password</label></td>';
echo '            <td><input type="password" id="password" name="password" size="35" required></td>';
echo '        </tr>';
echo '        <tr>';
echo '            <td><label id="roleLabel">Role</label></td>';
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
echo '    <a href="index.php?action=register">Register</a>';
echo '</div>';
