<?php

$tasks = array();
if (isset($_SESSION['tasks'])) {
    $tasks = $_SESSION['tasks'];
    unset($_SESSION['tasks']);
}

$submissions = array();
if (isset($_SESSION['submissions'])) {
    $submissions = $_SESSION['submissions'];
    unset($_SESSION['submissions']);
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

$assignmentTitle = '';
if (isset($_SESSION['assignmentTitle'])) {
    $assignmentTitle = $_SESSION['assignmentTitle'];
}

function viewSubmittedCode($maxSubmission, $remainingSubmissions)
{
    if ($maxSubmission == $remainingSubmissions) {
        return 'No submitted code';
    } else {
        return 'View code and results';
    }
}

echo '<a href="index.php?action=student">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a>';
echo '<br>';
echo '<br>';

echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Title</th>';
echo '        <th>Description</th>';
echo '        <th>Grade</th>';
echo '        <th>Max submissions</th>';
echo '        <th>Remaining submissions</th>';
echo '        <th>Tests</th>';
echo '        <th>Import code</th>';
echo '        <th>View submitted code and results</th>';
echo '    </tr>';

$index = 0;
foreach ($tasks as $task) {
    echo '    <tr>';
    echo '        <td>' . $task['title'] . '</td>';
    echo '        <td>' . $task['description'] . '</td>';
    if ($task['release_grade'] == 0) {
        echo '    <td></td>';
    } else {
        echo '    <td>' . $task['final_grade'] . '</td>';
    }
    echo '        <td>' . $task['try'] . '</td>';
    echo '        <td>' . ($task['try'] - $submissions[$index]) . '</td>';
    echo '        <td><a href="index.php?action=studentTests&id=' . $task['id'] . '">View Tests</a></td>';
    echo '        <td><a href="index.php?action=addCode&id=' . $task['id'] . '">Add code</a></td>';
    echo '        <td><a href="index.php?action=viewCode&id=' . $task['id'] . '">' . viewSubmittedCode($task['try'],
            ($task['try'] - $submissions[$index])) . '</a></td>';
    echo '    </tr>';
    $index++;
}
echo '</table>';

echo '<br>';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Back to Assignment</a>';