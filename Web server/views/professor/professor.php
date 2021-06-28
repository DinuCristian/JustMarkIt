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
echo '        <th>Assignment</th>';
echo '        <th>Enrolled</th>';
echo '        <th>Edit</th>';
echo '        <th>Delete</th>';
echo '    </tr>';

foreach ($courses as $course) {
    echo '    <tr>';
    echo '        <td>' . $course['title'] . '</td>';
    echo '        <td>' . $course['description'] . '</td>';
    echo '        <td>' . $course['year'] . '</td>';
    echo '        <td>' . $course['semester'] . '</td>';
    echo '        <td><a href="index.php?action=viewAssignments&id=' . $course['id'] . '">View assignments</a></td>';
    echo '        <td><a href="index.php?action=viewEnrolledStudents&id=' . $course['id'] . '">View enrolled students</a></td>';
    echo '        <td><a href="index.php?action=editCourse&id=' . $course['id'] . '">Edit</a></td>';
    echo '        <td><a href="index.php?action=deleteCourse&id=' . $course['id'] . '">Delete</a></td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<a href="index.php?action=addCourse">Add Course</a>';
