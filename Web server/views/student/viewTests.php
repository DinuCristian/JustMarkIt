<?php

$tests = array();
if (isset($_SESSION['tests'])) {
    $tests = $_SESSION['tests'];
    unset($_SESSION['tests']);
}

$testsCodingStyle= array();
if (isset($_SESSION['testCodingStyle'])) {
    $testsCodingStyle = $_SESSION['testCodingStyle'];
    unset($_SESSION['testCodingStyle']);
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$taskId = 0;
if (isset($_SESSION['taskId'])) {
    $taskId = $_SESSION['taskId'];
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

$assignmentTitle = '';
if (isset($_SESSION['assignmentTitle'])) {
    $assignmentTitle = $_SESSION['assignmentTitle'];
}

$taskTitle = '';
if (isset($_SESSION['taskTitle'])) {
    $taskTitle = $_SESSION['taskTitle'];
}


echo '<a href="index.php?action=student">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a> » ';
echo '<a href="index.php?action=studentTasks&id=' . $assignmentId . '">Task: ' . $taskTitle . '</a>';
echo '<br>';
echo '<br>';


if (!empty($testsCodingStyle)) {
    echo '<table class="list-table">';
    echo '    <tr>';
    echo '        <th>Description</th>';
    echo '        <th>Accepted number of violations</th>';
    echo '        <th>Penalty per violation</th>';
    echo '        <th>Grade</th>';
    echo '        <th>Details</th>';
    echo '    </tr>';

    foreach ($testsCodingStyle as $testCodingStyle) {
        echo '    <tr>';
        echo '        <td>' . $testCodingStyle['description'] . '</td>';
        echo '        <td>' . $testCodingStyle['input_test'] . '</td>';
        echo '        <td>' . $testCodingStyle['output_test'] . '</td>';
        echo '        <td>' . $testCodingStyle['grade'] . '</td>';
        echo '        <td><a target="_blank" href="'. $testCodingStyle['url'] .'">' . $testCodingStyle['name'] . '</a></td>';
        echo '    </tr>';
    }
    echo '</table>';
    echo '<br>';
    echo '<br>';
}

echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Description</th>';
echo '        <th>Input</th>';
echo '        <th>Output</th>';
echo '        <th>Grade</th>';
echo '    </tr>';

foreach ($tests as $test) {
    echo '    <tr>';
    echo '        <td>' . $test['description'] . '</td>';
    echo '        <td>' . $test['input_test'] . '</td>';
    echo '        <td>' . $test['output_test'] . '</td>';
    echo '        <td>' . $test['grade'] . '</td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<a href="index.php?action=studentTasks&id=' . $assignmentId . '">Back to Tasks</a>';
