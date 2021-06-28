<?php

$assignments = array();
if (isset($_SESSION['assignments'])) {
    $assignments = $_SESSION['assignments'];
    unset($_SESSION['assignments']);
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

echo '<a href="index.php?action=student">Course: ' . $courseTitle . '</a>';
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Title</th>';
echo '        <th>Description</th>';
echo '        <th>Due Date</th>';
echo '        <th>Grade</th>';
echo '        <th>Report</th>';
echo '        <th>Task</th>';
echo '    </tr>';

foreach ($assignments as $assignment) {
    echo '    <tr>';
    echo '        <td>' . $assignment['title'] . '</td>';
    echo '        <td>' . $assignment['description'] . '</td>';
    echo '        <td>' . date("d-m-Y H:i", strtotime($assignment['due_date'])) . '</td>';
    if ($assignment['release_grade'] == 0) {
    echo '        <td>N.A.</td>';
    } else {
    echo '        <td>' . number_format((float)$assignment['grade'], 2, '.', '') . '%</td>';
    }
    if ($assignment['release_grade'] == 0) {
        echo '    <td>N.A.</td>';
    } else {
        echo '    <td><a href="index.php?action=studentViewReports&id=' . $assignment['id'] . '">View report</a></td>';
    }
    echo '        <td><a href="index.php?action=studentTasks&id=' . $assignment['id'] . '">View tasks</a></td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<a href="index.php?action=student">Back to Courses</a>';
