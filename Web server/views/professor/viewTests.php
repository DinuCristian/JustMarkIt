<?php

$tests = array();
if (isset($_SESSION['tests'])) {
    $tests = $_SESSION['tests'];
    unset($_SESSION['tests']);
}

$testsCodingStyle = array();
if (isset($_SESSION['testCodingStyle'])) {
    $testsCodingStyle = $_SESSION['testCodingStyle'];
    unset($_SESSION['testCodingStyle']);
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$taskId = 0;
if (isset($_SESSION['taskId'])) {
    $taskId = $_SESSION['taskId'];
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

$runningTestsProfessor = '';
if (isset($_SESSION['runningTestsProfessor'])) {
    $runningTestsProfessor = $_SESSION['runningTestsProfessor'];
    unset($_SESSION['runningTestsProfessor']);
}

function isVisible($checked)
{
    if ($checked == 1)
        return 'Public';
    elseif ($checked == 2)
        return 'Protected';
    else
        return 'Private';
}


echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a> » ';
echo '<a href="index.php?action=viewTasks&id=' . $assignmentId . '">Task: ' . $taskTitle . '</a>';
echo '<br>';
echo '<br>';


if ($runningTestsProfessor == 0) {
    echo '<form action="" method="get">';
    echo '    Run tests for all submitted code ';
    echo '    <button name="action" type="submit" value="runTests">Run</button>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $taskId . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '</form>';
    echo '<br>';
} else {
    echo 'The tests are running for ' . $runningTestsProfessor . ' submissions';
}

if (empty($testsCodingStyle)) {
    echo '<a href="index.php?action=addTestCodingStyle&id=' . $taskId . '">Add coding style test</a> ';
    echo '<br>';
    echo '<br>';
} else {
    echo '<table class="list-table">';
    echo '    <tr>';
    echo '        <th>Description</th>';
    echo '        <th>Accepted number of violations</th>';
    echo '        <th>Penalty per violation</th>';
    echo '        <th>Grade</th>';
    echo '        <th>Visible for students</th>';
    echo '        <th>Description</th>';
    echo '        <th>Edit</th>';
    echo '        <th>Delete</th>';
    echo '    </tr>';

    foreach ($testsCodingStyle as $testCodingStyle) {
        echo '    <tr>';
        echo '        <td>' . $testCodingStyle['description'] . '</td>';
        echo '        <td>' . $testCodingStyle['input_test'] . '</td>';
        echo '        <td>' . $testCodingStyle['output_test'] . '</td>';
        echo '        <td>' . $testCodingStyle['grade'] . '</td>';
        echo '        <td>' . $testCodingStyle['type'] . '</td>';
        echo '        <td><a target="_blank" href="' . $testCodingStyle['url'] . '">' . $testCodingStyle['name'] . '</a></td>';
        echo '        <td><a href="index.php?action=editTestCodingStyle&id=' . $testCodingStyle['id'] . '">Edit</a></td>';
        echo '        <td><a href="index.php?action=deleteTestCodingStyle&id=' . $testCodingStyle['id'] . '">Delete</a></td>';
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
echo '        <th>Visible for students</th>';
echo '        <th>Edit</th>';
echo '        <th>Delete</th>';
echo '    </tr>';

foreach ($tests as $test) {
    echo '    <tr>';
    echo '        <td>' . $test['description'] . '</td>';
    echo '        <td>' . $test['input_test'] . '</td>';
    echo '        <td>' . $test['output_test'] . '</td>';
    echo '        <td>' . $test['grade'] . '</td>';
    echo '        <td>' . $test['type'] . '</td>';
    echo '        <td><a href="index.php?action=editTest&id=' . $test['id'] . '">Edit</a></td>';
    echo '        <td><a href="index.php?action=deleteTest&id=' . $test['id'] . '">Delete</a></td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<a href="index.php?action=addTest&id=' . $taskId . '">Add Test</a> ';
echo '<a href="index.php?action=viewTasks&id=' . $assignmentId . '">Back to Tasks</a>';
