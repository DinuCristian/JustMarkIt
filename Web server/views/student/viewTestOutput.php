<?php

$taskId = 0;
if (isset($_SESSION['taskId'])) {
    $taskId = $_SESSION['taskId'];
}

$submissionTests = '';
if (isset($_SESSION['submissionTests'])) {
    $submissionTests = $_SESSION['submissionTests'];
    unset($_SESSION['submissionTests']);
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$checkGradesReleased = false;
if (isset($_SESSION['checkGradesReleased'])) {
    $checkGradesReleased = $_SESSION['checkGradesReleased'];
    unset($_SESSION['checkGradesReleased']);
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

function isPassed ($status) {
    if ($status == 1) {
        return '<div style="color: green">Passed</div>';
    }
    return '<div style="color: red">Failed</div>';
}

function isVisible ($visible) {
    if ($visible == 'public') {
        return 'Visible';
    }
    return 'Not visible';
}

function obtainedMark ($status, $grade) {
    if ($status == 1) {
        return $grade;
    }
    return 0;
}

echo '<a href="index.php?action=student">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a> » ';
echo '<a href="index.php?action=studentTasks&id=' . $assignmentId . '">Task: ' . $taskTitle . '</a> » ';
echo '<a href="index.php?action=viewCode&id=' . $taskId . '">View Code</a>';
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Submission version</th>';
echo '        <th>Test description</th>';
echo '        <th>Visible to student</th>';
echo '        <th>Input</th>';
echo '        <th>Expected output</th>';
echo '        <th>Actual output</th>';
echo '        <th>Passed</th>';
echo '        <th>Available grade</th>';
echo '        <th>Obtained grade</th>';
echo '    </tr>';

foreach ($submissionTests as $submissionTest) {
    if ($submissionTest['type'] != 'public' && !$checkGradesReleased) {
        continue;
    }

    if ($submissionTest['coding_standard'] != 1) {
        echo '<tr>';
        echo '  <td class="hidden-cell"></td>';
        echo '  <td class="hidden-cell"></td>';
        echo '  <td class="hidden-cell"></td>';
        echo '  <td><b>Accepted number of violations</b></td>';
        echo '  <td><b>Penalty per violation</b></td>';
        echo '  <td colspan="4" class="hidden-cell"></td>';
        echo '</tr>';
    }

    echo '    <tr>';
    echo '        <td>' . $submissionTest['version'] . '</td>';
    echo '        <td>' . $submissionTest['description'] . '</td>';
    echo '        <td>' . isVisible($submissionTest['type']) . '</td>';
    echo '        <td>' . $submissionTest['input_test'] . '</td>';
    echo '        <td>' . $submissionTest['output_test'] . '</td>';
    echo '        <td>' . $submissionTest['output'] . '</td>';
    echo '        <td>' . isPassed($submissionTest['status']) . '</td>';
    echo '        <td>' . $submissionTest['grade'] . '</td>';
    if ($submissionTest['coding_standard'] != 1) {
        $violations = substr_count( $submissionTest['output'], "\n" );
        if ($violations >= 2) {
            $violations -= 1;
            if ($submissionTest['coding_standard'] == 3) {
                $violations -= 1;
            }
        }

        if ($submissionTest['status']) {
            if ($violations <= $submissionTest['input_test']) {
                echo '<td>' . $submissionTest['grade'] . '</td>';
            } else {
                $violations -= $submissionTest['input_test'];
                echo '<td>' . max(0, $submissionTest['grade'] - $submissionTest['output_test'] * $violations) . '</td>';
            }
        } else {
            echo '<td>0</td>';
        }
    } else {
        echo '        <td>' . obtainedMark($submissionTest['status'], $submissionTest['grade']) . '</td>';
    }
    echo '    </tr>';
}
echo '</table>';

echo '<br>';
echo '<a href="index.php?action=viewCode&id=' . $taskId . '">Back to Submitted Code</a>';