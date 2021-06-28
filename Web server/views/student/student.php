<?php

$courses = array();
if (isset($_SESSION['courses'])) {
    $courses = $_SESSION['courses'];
    unset($_SESSION['courses']);
}

echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Title</th>';
echo '        <th>Description</th>';
echo '        <th>Year</th>';
echo '        <th>Semester</th>';
echo '        <th>Grade</th>';
echo '        <th>Assignment</th>';
echo '    </tr>';

foreach ($courses as $course) {
    echo '    <tr>';
    echo '        <td>' . $course['title'] . '</td>';
    echo '        <td>' . $course['description'] . '</td>';
    echo '        <td>' . $course['year'] . '</td>';
    echo '        <td>' . $course['semester'] . '</td>';
    echo '        <td>' . number_format((float)$course['grade'], 2, '.', '') . '%</td>';
    echo '        <td><a href="index.php?action=studentAssignments&id=' . $course['id'] . '">View Assignments</a></td>';
    echo '    </tr>';
}
echo '</table>';
