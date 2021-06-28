<?php

$enrolledStudents = array();
if (isset($_SESSION['enrolledStudents'])) {
    $enrolledStudents = $_SESSION['enrolledStudents'];
    unset($_SESSION['enrolledStudents']);
}

$studentCount = 0;
if (isset($_SESSION['studentCount'])) {
    $studentCount = $_SESSION['studentCount'];
    unset($_SESSION['studentCount']);
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a>';
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Email</th>';
echo '        <th>Name</th>';
echo '        <th>Surname</th>';
echo '    </tr>';

foreach ($enrolledStudents as $enrolledStudent) {
    echo '    <tr>';
    echo '        <td>' . $enrolledStudent['email'] . '</td>';
    echo '        <td>' . $enrolledStudent['name'] . '</td>';
    echo '        <td>' . $enrolledStudent['surname'] . '</td>';
    echo '    </tr>';
}
echo '</table>';

echo nl2br("There are " . $studentCount . " students enrolled in this course. \n");
echo '<br>';
echo '<a href="index.php?action=professor">View All Courses</a>';
