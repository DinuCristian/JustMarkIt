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

$runningTestsProfessor = '';
if (isset($_SESSION['runningTestsProfessor'])) {
    $runningTestsProfessor = $_SESSION['runningTestsProfessor'];
    unset($_SESSION['runningTestsProfessor']);
}

function isEmpty($value)
{
    if ($value == '')
        return 0;
    return $value;
}


echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a>';
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Title</th>';
echo '        <th>Description</th>';
echo '        <th>Try</th>';
echo '        <th>Submissions</th>';
echo '        <th>Task Percentage</th>';
echo '        <th>Tests</th>';
echo '        <th>Edit</th>';
echo '        <th>Delete</th>';
echo '    </tr>';

$index = 0;
foreach ($tasks as $task) {
    echo '    <tr>';
    echo '        <td>' . $task['title'] . '</td>';
    echo '        <td>' . $task['description'] . '</td>';
    echo '        <td>' . $task['try'] . '</td>';
    echo '        <td>' . isEmpty($submissions[$index]) . '</td>';
    echo '        <td>' . $task['grade_percentage'] . '%</td>';
    echo '        <td><a href="index.php?action=viewTests&id=' . $task['id'] . '">View Tests</a></td>';
    echo '        <td><a href="index.php?action=editTask&id=' . $task['id'] . '">Edit</a></td>';
    echo '        <td><a href="index.php?action=deleteTask&id=' . $task['id'] . '">Delete</a></td>';
    echo '    </tr>';
    $index++;
}
echo '</table>';

echo '<br>';
echo '<a href="index.php?action=addTask&id=' . $assignmentId . '">Add Task</a> ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Back to Assignment</a>';