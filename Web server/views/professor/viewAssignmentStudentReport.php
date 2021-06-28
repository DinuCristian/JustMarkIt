<?php

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$reports = array();
if (isset($_SESSION['reports'])) {
    $reports = $_SESSION['reports'];
    unset($_SESSION['reports']);
}

$assignmentSubmissionNo = array();
if (isset($_SESSION['assignmentSubmissionNo'])) {
    $assignmentSubmissionNo = $_SESSION['assignmentSubmissionNo'];
    unset($_SESSION['assignmentSubmissionNo']);
}

$studentDetails = array();
if (isset($_SESSION['studentDetails'])) {
    $studentDetails= $_SESSION['studentDetails'];
    unset($_SESSION['studentDetails']);
}

function isPassed ($status) {
    if ($status == 1) {
        return '<div style="color: green">Passed</div>';
    }
    return '<div style="color: red">Failed</div>';
}

function obtainedMark($status, $grade) {
    if ($status == 1) {
        return $grade;
    }
    return 0;
}

function getSubmissions($assignmentSubmissionNo, $taskId) {
    foreach ($assignmentSubmissionNo as $item) {
        if ($taskId == $item['id']) {
            return $item['used_try'] . '/' . $item['try'] . ' submission(s)';
        }
    }
}

echo '<b>Student:</b> ' . $studentDetails['name'] . ' ' . $studentDetails['surname'];
echo '<br>';
echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Test Description</th>';
echo '        <th>Input</th>';
echo '        <th>Expected Output</th>';
echo '        <th>Actual Output</th>';
echo '        <th>Test Type</th>';
echo '        <th>Passed</th>';
echo '        <th>Available Grade</th>';
echo '        <th>Obtained Grade</th>';
echo '    </tr>';

$previous_class_name = '';
$previous_grade_percentage = 0;
$taskGrade = 0;
$maxTaskGrade = 0;
$assignmentGrade = 0;

foreach ($reports as $report) {
    if ($report['class_name'] != $previous_class_name) {
        if ($previous_class_name != '') {
            echo '<tr>';
            echo '    <td colspan="5" class="hidden-cell"></td>';
            echo '    <td><b>Total</b></td>';
            echo '    <td style="text-align: right"><b>' . $maxTaskGrade . '</b></td>';
            echo '    <td style="text-align: right"><b>' . $taskGrade . '</b></td>';
            echo '</tr>';

            $taskPercentageGrade = $previous_grade_percentage / $maxTaskGrade * $taskGrade;

            echo '<tr>';
            echo '    <td colspan="5" class="hidden-cell"></td>';
            echo '    <td><b>%</b></td>';
            echo '    <td style="text-align: right"><b>' . $previous_grade_percentage . '</b></td>';
            echo '    <td style="text-align: right"><b>' . number_format((float)$taskPercentageGrade, 2, '.', '') . '</b></td>';
            echo '</tr>';

            $assignmentGrade += $taskPercentageGrade;
        }

        echo '<tr class="hidden-row">';
        echo '    <td colspan="8">&nbsp;</td>';
        echo '</tr>';

        echo '<tr>';
        echo '  <td  class="hidden-cell"><b>Task: ' . $report['class_name'] . ' (' . $report['grade_percentage'] . '%)</b></td>';
        echo '  <td colspan="7" class="hidden-cell" style="text-align: right"><b>' . getSubmissions($assignmentSubmissionNo, $report['task_id']) . '</b></td>';
        echo '</tr>';

        $taskGrade = 0;
        $maxTaskGrade = 0;
    }

    $previous_class_name = $report['class_name'];
    $taskTestGrade = obtainedMark($report['status'], $report['grade']);
    $maxTaskGrade += $report['grade'];
    $previous_grade_percentage = $report['grade_percentage'];

    if ($report['coding_standard'] != 1) {
        echo '<tr>';
        echo '  <td class="hidden-cell"></td>';
        echo '  <td><b>Accepted number of violations</b></td>';
        echo '  <td><b>Penalty per violation</b></td>';
        echo '  <td colspan="5" class="hidden-cell"></td>';
        echo '</tr>';
    }

    echo '    <tr>';
    echo '        <td>';
                    if ($report['coding_standard'] != 1) {
                        echo '<b>Coding standard:</b> <a target="_blank" href = "' . $report['url'] . '" > ' . $report['coding_name'] . ' </a >';
                    } else {
                        echo $report['description'];
                    }
    echo '        </td>';
    echo '        <td>' . $report['input_test'] . '</td>';
    echo '        <td>' . $report['output_test'] . '</td>';
    echo '        <td>' . $report['output'] . '</td>';
    echo '        <td>' . $report['type'] . '</td>';
    echo '        <td>' . isPassed($report['status']) . '</td>';
    echo '        <td style="text-align: right">' . $report['grade'] . '</td>';
    if ($report['coding_standard'] != 1) {
        $violations = substr_count( $report['output'], "\n" );
        if ($violations >= 2) {
            $violations -= 1;
            if ($report['coding_standard'] == 3) {
                $violations -= 1;
            }
        }

        if ($report['status']) {
            if ($violations <= $report['input_test']) {
                echo '<td>' . $report['grade'] . '</td>';
            } else {
                $violations -= $report['input_test'];
                $taskTestGrade = max(0, $report['grade'] - $report['output_test'] * $violations);
                echo '<td>' . $taskTestGrade . '</td>';
            }
        } else {
            $taskTestGrade = 0;
            echo '<td>0</td>';
        }
    } else {
        echo '        <td style="text-align: right">' . $taskTestGrade . '</td>';
    }
    echo '    </tr>';
    $taskGrade += $taskTestGrade;
}

echo '<tr>';
echo '    <td colspan="5" class="hidden-cell"></td>';
echo '    <td><b>Total</b></td>';
echo '    <td style="text-align: right"><b>' . $maxTaskGrade . '</b></td>';
echo '    <td style="text-align: right"><b>' . $taskGrade . '</b></td>';
echo '</tr>';

$taskPercentageGrade = $report['grade_percentage'] / $maxTaskGrade * $taskGrade;

echo '<tr>';
echo '    <td colspan="5" class="hidden-cell"></td>';
echo '    <td><b>%</b></td>';
echo '    <td style="text-align: right"><b>' . $report['grade_percentage'] . '</b></td>';
echo '    <td style="text-align: right"><b>' . number_format((float)$taskPercentageGrade, 2, '.', '') . '</b></td>';
echo '</tr>';

echo '<tr class="hidden-row">';
echo '    <td colspan="8">&nbsp;</td>';
echo '</tr>';

$assignmentGrade += $taskPercentageGrade;

echo '<tr>';
echo '    <td colspan="5"><b>Assignment Mark</b></td>';
echo '    <td><b>%</b></td>';
echo '    <td style="text-align: right"><b>100</b></td>';
echo '    <td style="text-align: right"><b>' . number_format((float)$assignmentGrade, 2, '.', '') . '</b></td>';
echo '</tr>';

echo '</table>';

echo '<br>';
echo '<a href="index.php?action=professorViewReports&id=' . $assignmentId . '">Back to Assignment Report</a>';
