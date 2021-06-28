<?php

$courses = array();
if (isset($_SESSION['courses'])) {
    $courses = $_SESSION['courses'];
    unset($_SESSION['courses']);
}

echo '<a href="index.php?action=administrator">Administrator view all requests</a>';
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Course</th>';
echo '        <th>Leader</th>';
echo '        <th>View enrolled students</th>';
echo '        <th>Add new students</th>';
echo '    </tr>';

foreach ($courses as $course) {
    echo '    <tr>';
    echo '        <td>' . $course['title'] . '</td>';
    echo '        <td>' . $course['name'] . ' '. $course['surname'] .'</td>';
    echo '        <td style="text-align: center"><a href="index.php?action=adminViewCourseStudents&id=' . $course['id'] . '">View</a></td>';
    echo '        <td style="text-align: center"><a href="index.php?action=adminAddStudentsToCourse&id=' . $course['id'] . '">Add</a></td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<a href="index.php?action=administrator">Back</a>';