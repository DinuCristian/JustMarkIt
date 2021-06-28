<?php

$users = array();
if (isset($_SESSION['users'])) {
    $users = $_SESSION['users'];
    unset($_SESSION['users']);
}

$numberOfPages = 1;
if (isset($_SESSION['numberOfPages'])) {
    $numberOfPages = $_SESSION['numberOfPages'];
    unset($_SESSION['numberOfPages']);
}

$filterProfessor = 'false';
if (isset($_SESSION['filterProfessor'])) {
    $filterProfessor = $_SESSION['filterProfessor'];
    unset($_SESSION['filterProfessor']);
}

$filterStudent = 'false';
if (isset($_SESSION['filterStudent'])) {
    $filterStudent = $_SESSION['filterStudent'];
    unset($_SESSION['filterStudent']);
}

$filterValidUser = 'false';
if (isset($_SESSION['filterValidUser'])) {
    $filterValidUser = $_SESSION['filterValidUser'];
    unset($_SESSION['filterValidUser']);
}

$filterInvalidUser = 'false';
if (isset($_SESSION['filterInvalidUser'])) {
    $filterInvalidUser = $_SESSION['filterInvalidUser'];
    unset($_SESSION['filterInvalidUser']);
}

function displayValid($valid) {
    if ($valid == 1)
        return 'True';
    return 'False';
}

function isChecked($checked) {
    if ($checked == 'true')
        return 'checked';
    return '';
}

// Filter
echo '<form action="" method="get">';
echo '<input type="hidden" name="action" value="administrator">';
echo '    <input type="checkbox" id="filterProfessor" name="filterProfessor" value="true"' . isChecked($filterProfessor)
    . '>';
echo '    <label for="filterProfessor">Role: professor</label><br>';
echo '    <input type="checkbox" id="filterStudent" name="filterStudent" value="true"' . isChecked($filterStudent)
    . '>';
echo '    <label for="filterStudent">Role: student</label><br>';
echo '    <input type="checkbox" id="filterValidUser" name="filterValidUser" value="true"' . isChecked($filterValidUser)
    . '>';
echo '    <label for="validUser">User: valid</label><br>';
echo '    <input type="checkbox" id="filterInvalidUser" name="filterInvalidUser" value="true"'
    . isChecked($filterInvalidUser) . '>';
echo '    <label for="invalidUser">User: invalid</label><br>';
echo '<input type="submit" value="Filter">';
echo '</form>';


echo '<a href="index.php?action=viewCourses">View all courses</a>';
echo '<br>';
echo '<a href="index.php?action=bulkEnrollment">Bulk enrollment</a>';
echo '<br>';
echo '<br>';


// Display all users
echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Email</th>';
echo '        <th>Name</th>';
echo '        <th>Surname</th>';
echo '        <th>Valid Until</th>';
echo '        <th>Role</th>';
echo '        <th>Valid</th>';
echo '        <th>Edit</th>';
echo '        <th>Validate/Invalidate</th>';
echo '        <th>Delete</th>';
echo '    </tr>';

foreach ($users as $user) {
    echo '    <tr>';
    echo '        <td>' . $user['email'] . '</td>';
    echo '        <td>' . $user['name'] . '</td>';
    echo '        <td>' . $user['surname'] . '</td>';
    echo '        <td>' . date("d-m-Y", strtotime($user['end_date'])) . '</td>';
    echo '        <td>' . $user['role'] . '</td>';
    echo '        <td>' . displayValid($user['validated']) . '</td>';
    echo '        <td><a href="index.php?action=editUser&id=' . $user['id'] . '">Edit</a></td>';
    echo '        <td style="text-align: center">';
                      if ($user['validated'] ==1) {
                          echo '<a href="index.php?action=invalidateUser&id=' . $user['id'] . '">Invalidate</a>';
                      } else {
                          echo '<a href="index.php?action=validateUser&id=' . $user['id'] . '">Validate</a>';
                      }
    echo '        </td>';
    echo '        <td><a href="index.php?action=deleteUser&id=' . $user['id'] . '">Delete</a></td>';
    echo '    </tr>';
}
echo '</table>';


// Diplay pages for users table
echo '<table class="list-pages">';
echo '    <tr>';

$url = "index.php?action=administrator";
if ($filterProfessor == 'true') {
    $url .= "&filterProfessor=" . $filterProfessor;
}
if ($filterStudent == 'true') {
    $url .= "&filterStudent=" . $filterStudent;
}
if ($filterValidUser == 'true') {
    $url .= "&filterValidUser=" . $filterValidUser;
}
if ($filterInvalidUser == 'true') {
    $url .= "&filterInvalidUser=" . $filterInvalidUser;
}

echo '<td>Page: </td>';
for ($page = 1; $page <= $numberOfPages; $page++) {
    echo '<td><a href="' . $url . '&page=' . $page . '">' . $page . '</a></td>';
}

echo '    </tr>';
echo '</table>';
echo '<br>';
echo '<br>';
