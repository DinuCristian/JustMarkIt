<!--Assignment graph-->
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
                subtitle: 'Assignment: ' + document.getElementById('assignmentName').value
            }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
</script>


<!--Task graph-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var taskCount = document.getElementById('taskCount').value;

        for (var i = 0; i < taskCount; i++) {
            var x = document.getElementById('gradesTasksIntervalCount' + i).value.split(',');
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
                colors: ['#f89828'],
                chart: {
                    title: 'Grade Distribution',
                    subtitle: 'Task: ' + document.getElementById('taskName' + i).value,
                }
            };

            var chart = new google.charts.Bar(document.getElementById('columnchart' + i));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    }
</script>


<?php

$gradesIntervalCount = array();
if (isset($_SESSION['gradesIntervalCount'])) {
    $gradesIntervalCount = $_SESSION['gradesIntervalCount'];
    unset($_SESSION['gradesIntervalCount']);
}

$gradesTasksIntervalCount = array();
if (isset($_SESSION['gradesTasksIntervalCount'])) {
    $gradesTasksIntervalCount = $_SESSION['gradesTasksIntervalCount'];
    unset($_SESSION['gradesTasksIntervalCount']);
}

$taskGradesForStudents = array();
if (isset($_SESSION['taskGradesForStudents'])) {
    $taskGradesForStudents = $_SESSION['taskGradesForStudents'];
    unset($_SESSION['taskGradesForStudents']);
}

$assignmentGradesForStudents = array();
if (isset($_SESSION['assignmentGradesForStudents'])) {
    $assignmentGradesForStudents = $_SESSION['assignmentGradesForStudents'];
    unset($_SESSION['assignmentGradesForStudents']);
}

$tasksTitle = array();
if (isset($_SESSION['tasksTitle'])) {
    $tasksTitle = $_SESSION['tasksTitle'];
    unset($_SESSION['tasksTitle']);
}

$taskMeanGrades= array();
if (isset($_SESSION['taskMeanGrades'])) {
    $taskMeanGrades = $_SESSION['taskMeanGrades'];
    unset($_SESSION['taskMeanGrades']);
}

$assignmentName = '';
if (isset($_SESSION['assignmentName'])) {
    $assignmentName = $_SESSION['assignmentName'];
    unset($_SESSION['assignmentName']);
}

$checkReleaseAssignmentGrades = '';
if (isset($_SESSION['checkReleaseAssignmentGrades'])) {
    $checkReleaseAssignmentGrades = $_SESSION['checkReleaseAssignmentGrades'];
    unset($_SESSION['checkReleaseAssignmentGrades']);
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$taskCount = 0;
if (isset($_SESSION['taskCount'])) {
    $taskCount = $_SESSION['taskCount'];
    unset($_SESSION['taskCount']);
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

$assignmentTitle = '';
if (isset($_SESSION['assignmentTitle'])) {
    $assignmentTitle = $_SESSION['assignmentTitle'];
}

echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a>';
echo '<br>';
echo '<br>';


echo '<div id="columnchart_material" style="width: 800px; height: 500px; margin:0 auto;"></div>';
echo '<br>';

for ($index = 0; $index < $taskCount; $index++) {
    echo '<div id="columnchart' . $index . '" style="width: 800px; height: 500px; margin:0 auto;"></div>';
    echo '<br>';
}


// Release grades
if (!$checkReleaseAssignmentGrades) {
    echo '<form action="" method="get">';
    echo '    Release grades for all tasks ';
    echo '    <button name="action" type="submit" value="releaseAssignmentGrade">Release</button>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $assignmentId . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '</form>';
} else {
    echo '<b>Grades have been released.</b>';
    echo '<br>';
}
echo '<br>';


// Table for mean and median for each task
echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Task</th>';
echo '        <th>Mean Grade</th>';
echo '    </tr>';

foreach ($taskMeanGrades as $taskMeanGrade) {
    echo '<tr>';
    echo '  <th>' . $taskMeanGrade['title'] . '</th>';
    echo '  <td style="text-align: right">' . number_format($taskMeanGrade['mean_grade'], 2) . '%</td>';
    echo '</tr>';
}

echo '</table>';
echo '<br>';


// Table for each student
echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Student</th>';
echo '        <th>Assignment Grade</th>';
echo '    </tr>';

foreach ($assignmentGradesForStudents as $assignmentGradesForStudent) {
    echo '    <tr>';
    echo '        <td><a href="index.php?action=professorViewStudentReports&id=' . $assignmentGradesForStudent['user_id'] . '">' . $assignmentGradesForStudent['name'] . ' ' . $assignmentGradesForStudent['surname'] . '</a></td>';
    echo '        <td style="text-align: right">' . number_format($assignmentGradesForStudent['grade'], 0) . '%</td>';
    echo '    </tr>';
}

echo '</table>';


// Assignment graph values
echo '<input id="gradesIntervalCount" type="hidden" value="' . $gradesIntervalCount . '" />';
echo '<input id="assignmentName" type="hidden" value="' . $assignmentName . '" />';

// Task graph values
echo '<input id="taskCount" type="hidden" value="' . $taskCount . '" />';
if (!empty($gradesTasksIntervalCount)) {
    for ($index = 0; $index < $taskCount; $index++) {
        echo '<input id="gradesTasksIntervalCount' . $index . '" type="hidden" value="' . $gradesTasksIntervalCount[$index]['grade'] . '" />';
        echo '<input id="taskName' . $index . '" type="hidden" value="' . $tasksTitle[$index]['title'] . '" />';
    }
}


echo '<br>';
echo '<a href="" onclick="window.print();" class="noPrint">Print Report</a>';
echo '<br>';

echo '<br>';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Back to Assignment</a>';