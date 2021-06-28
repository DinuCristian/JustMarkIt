<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var x = document.getElementById('gradesIntervalCount').value.split(',');
        var data = google.visualization.arrayToDataTable([
            ['Grade', 'Number of students'],
            ['0-9', parseInt(x[0])],
            ['10-19', parseInt(x[1])],
            ['20-29', parseInt(x[2])],
            ['30-39', parseInt(x[3])],
            ['40-49', parseInt(x[4])],
            ['50-59', parseInt(x[5])],
            ['60-69', parseInt(x[6])],
            ['70-79', parseInt(x[7])],
            ['80-89', parseInt(x[8])],
            ['90-99', parseInt(x[9])],
            ['100', parseInt(x[10])]
        ]);

        var options = {
            colors: ['#003976'],
            chart: {
                title: 'Grade Distribution',
                subtitle: 'Assignment: ' + document.getElementById('assignmentName').value,
            }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
</script>

<?php

$reports = array();
if (isset($_SESSION['reports'])) {
    $reports = $_SESSION['reports'];
    unset($_SESSION['reports']);
}

$gradesIntervalCount = array();
if (isset($_SESSION['gradesIntervalCount'])) {
    $gradesIntervalCount = $_SESSION['gradesIntervalCount'];
    unset($_SESSION['gradesIntervalCount']);
}

$assignmentSubmissionNo = array();
if (isset($_SESSION['assignmentSubmissionNo'])) {
    $assignmentSubmissionNo = $_SESSION['assignmentSubmissionNo'];
    unset($_SESSION['assignmentSubmissionNo']);
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$assignmentName = '';
if (isset($_SESSION['assignmentName'])) {
    $assignmentName = $_SESSION['assignmentName'];
    unset($_SESSION['assignmentName']);
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

$assignmentTitle = '';
if (isset($_SESSION['assignmentTitle'])) {
    $assignmentTitle = $_SESSION['assignmentTitle'];
}

function isPassed ($status) {
    if ($status == 1) {
        return '<div style="color: green">Passed</div>';
    }
    return '<div style="color: red">Failed</div>';
}

function obtainedMark ($status, $grade) {
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

echo '<a href="index.php?action=student">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a>';
echo '<br>';
echo '<br>';

echo '<div id="columnchart_material" style="width: 800px; height: 500px; margin:0 auto;"></div>';
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
        echo '  <td class="hidden-cell"><b>Task: ' . $report['class_name'] . ' (' . $report['grade_percentage'] . '%)</b></td>';
        echo '  <td colspan="7" class="hidden-cell" style="text-align: right"><b>' . getSubmissions($assignmentSubmissionNo, $report['task_id']) . '</b></td>';

        echo '</tr>';

        $taskGrade = 0;
        $maxTaskGrade = 0;
    }

    $previous_class_name = $report['class_name'];
    $taskTestGrade = obtainedMark($report['status'], $report['grade']);
    $taskGrade += $taskTestGrade;
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
    echo '        <td style="text-align: right">' . $taskTestGrade . '</td>';
    echo '    </tr>';
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

echo '<input id="gradesIntervalCount" type="hidden" value="' . $gradesIntervalCount .'" />';
echo '<input id="assignmentName" type="hidden" value="' . $assignmentName .'" />';


echo '<br>';
echo '<a href="" onclick="window.print();" class="noPrint">Print Report</a>';

echo '<br>';
echo '<br>';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Back to Assignment</a>';