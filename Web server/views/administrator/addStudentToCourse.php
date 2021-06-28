<?php

$users = array();
if (isset($_SESSION['users'])) {
    $users = $_SESSION['users'];
    unset($_SESSION['users']);
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
    unset($_SESSION['courseId']);
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
    unset($_SESSION['courseTitle']);
}

echo '<a href="index.php?action=administrator">Administrator view all requests</a> » ';
echo '<a href="index.php?action=viewCourses">Course: ' . $courseTitle . '</a>';
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Student</th>';
echo '        <th>Add to Course</th>';
echo '    </tr>';

foreach ($users as $user) {
    echo '    <tr>';
    echo '        <td>' . $user['name'] . ' '. $user['surname'] .'</td>';
    echo '        <td style="text-align: center"><a href="index.php?action=adminAddStudentToCourse&userId=' . $user['id'] . '&courseId=' . $courseId . '">Add</a></td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<a href="index.php?action=viewCourses">Back</a>';