<?php

$userId = 0;
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
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
if (isset($_SESSION['roleIdUserRequest'])) {
    $roleId = $_SESSION['roleIdUserRequest'];
    unset($_SESSION['roleIdUserRequest']);
}

$roles = '';
if (isset($_SESSION['roles'])) {
    $roles = $_SESSION['roles'];
    unset($_SESSION['roles']);
}

if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}

echo '<table>';
echo '    <form action="index.php?action=editUser&go=true" method="get">';
echo '        <tr>';
echo '            <td></td>';
echo '            <td><input type="hidden" name="action" value="editUser" size="35"></td>';
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
echo '            <td>Name</td>';
echo '            <td><input type="text" name="name" value="' . $name . '" size="35"></td>';
echo '        </tr>';
echo '        <tr>';
echo '            <td>Surname</td>';
echo '            <td><input type="text" name="surname" value="' . $surname . '" size="35"></td>';
echo '        </tr>';
echo '        <tr>';
echo '            <td>End Date</td>';
echo '            <td><input type="text" name="endDate" value="' . $endDate . '" size="35"></td>';
echo '        </tr>';
echo '        <tr>';
echo '            <td>Role</td>';
echo '            <td>';
echo '<select id="role" name="role">';
echo '<option value="0">Select role</option>';
foreach ($roles as $role) {
    if ($role['id'] == $roleId) {
        echo '<option value="' . $role['id'] . '" selected>' . $role['role'] . '</option>';
    } else {
        echo '<option value="' . $role['id'] . '">' . $role['role'] . '</option>';
    }
}
echo '</select>';
echo '           </td>';
echo '        </tr>';
echo '        <tr>';
echo '            <td><input type="submit" value="Save" name="submit"></td>';
echo '            <td><input type="reset" value="Reset" name="reset"></td>';
echo '        </tr>';
echo '    </form>';
echo '</table>';

echo '<a href="index.php?action=administrator">Requests</a>';