<?php

$assignments = array();
if (isset($_SESSION['assignments'])) {
    $assignments = $_SESSION['assignments'];
    unset($_SESSION['assignments']);
}

$submissions = array();
if (isset($_SESSION['submissions'])) {
    $submissions = $_SESSION['submissions'];
    unset($_SESSION['submissions']);
}

$unmarkedSubmissionsCount = array();
if (isset($_SESSION['unmarkedSubmissionsCount'])) {
    $unmarkedSubmissionsCount = $_SESSION['unmarkedSubmissionsCount'];
    unset($_SESSION['unmarkedSubmissionsCount']);
}

$runningTestsProfessor = array();
if (isset($_SESSION['runningTestsProfessor'])) {
    $runningTestsProfessor = $_SESSION['runningTestsProfessor'];
    unset($_SESSION['runningTestsProfessor']);
}

$enrolledStudents = 0;
if (isset($_SESSION['enrolledStudents'])) {
    $enrolledStudents = $_SESSION['enrolledStudents'];
    unset($_SESSION['enrolledStudents']);
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

function isEmpty($value)
{
    if ($value == '')
        return 0;
    return $value;
}

echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a>';
echo '<br>';
echo '<br>';


// Release grade for the course
//echo '<form action="" method="get">';
//echo '    Release grades for all assignments ';
//echo '    <button name="action" type="submit" value="releaseCourseGrade">Release</button>';
//echo '        <tr>';
//echo '            <td></td>';
//echo '            <td><input type="hidden" name="id" value="' . $courseId . '" size="35"></td>';
//echo '        </tr>';
//echo '        <tr>';
//echo '            <td></td>';
//echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
//echo '        </tr>';
//echo '</form>';
//echo '<br>';


echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Title</th>';
echo '        <th>Description</th>';
echo '        <th>Publish Date</th>';
echo '        <th>Due Date</th>';
echo '        <th>Submissions</th>';
echo '        <th>Assignment Percentage</th>';
echo '        <th>Report</th>';
echo '        <th>Run Tests</th>';
echo '        <th>Task</th>';
echo '        <th>Edit</th>';
echo '        <th>Delete</th>';
echo '    </tr>';

$index = 0;
foreach ($assignments as $assignment) {
    echo '    <tr>';
    echo '        <td>' . $assignment['title'] . '</td>';
    echo '        <td>' . $assignment['description'] . '</td>';
    echo '        <td>' . date("d-m-Y H:i", strtotime($assignment['publish_assignment_date'])) . '</td>';
    echo '        <td>' . date("d-m-Y H:i", strtotime($assignment['due_date'])) . '</td>';
    echo '        <td>' . isEmpty($submissions[$index]) . '/' . $enrolledStudents . '</td>';
    echo '        <td>' . $assignment['grade_percentage'] . '%</td>';
    echo '        <td>';
                        if ($unmarkedSubmissionsCount[$index] == 0 && $runningTestsProfessor[$index] == 0) {
                            echo '<a href="index.php?action=professorViewReports&id=' . $assignment['id'] . '">View report</a>';
                        } else {
                            echo 'N.A.';
                        }
    echo '        </td>';
    echo '        <td>';
                      if ($runningTestsProfessor[$index] == 0) {
                          echo '<a href="index.php?action=runAssignmentTests&id=' . $assignment['id'] . '&go=true">Run Tests</a>';
                      } else {
                          echo $runningTestsProfessor[$index] . ' test(s) running.';
                      }
    echo '        </td>';
    echo '        <td><a href="index.php?action=viewTasks&id=' . $assignment['id'] . '">View tasks</a></td>';
    echo '        <td><a href="index.php?action=editAssignment&id=' . $assignment['id'] . '">Edit</a></td>';
    echo '        <td><a href="index.php?action=deleteAssignment&id=' . $assignment['id'] . '">Delete</a></td>';
    echo '    </tr>';
    $index++;
}
echo '</table>';


echo '</br>';
echo '<a href="index.php?action=addAssignment&id=' . $courseId . '">Add Assignment</a>';
echo ' ';
echo '<a href="index.php?action=professor">Back to Courses</a>';
