<?php

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$taskId = 0;
if (isset($_SESSION['taskId'])) {
    $taskId = $_SESSION['taskId'];
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

$checkSubmittedCode = '';
if (isset($_SESSION['checkSubmittedCode'])) {
    $checkSubmittedCode = $_SESSION['checkSubmittedCode'];
    unset($_SESSION['checkSubmittedCode']);
}

function isFinal($final) {
    if ($final == 1) {
        return 'Yes';
    }
    return 'No';
}


echo '<a href="index.php?action=student">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=studentAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a> » ';
echo '<a href="index.php?action=studentTasks&id=' . $assignmentId . '">Task: ' . $taskTitle . '</a>';
echo '<br>';
echo '<br>';

echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Version</th>';
echo '        <th>Submitted date</th>';
echo '        <th>Partial grade<sup>1</sup></th>';
echo '        <th>Final grade</th>';
echo '        <th>Code</th>';
echo '        <th>Submission result</th>';
echo '        <th>Final</th>';
echo '        <th>Set Final</th>';
echo '        <th>View test output</th>';
echo '    </tr>';

foreach ($submissions as $submission) {
    echo '    <tr>';
    echo '        <td>' . $submission['version'] . '</td>';
    echo '        <td>' . $submission['date'] . '</td>';
    echo '        <td>' . $submission['partial_grade'] . '</td>';
    echo '        <td>';
                      if ($submission['release_grade'] == 0) {
                          echo '';
                      } else {
                          echo $submission['final_grade'];
                      }
    echo '        </td>';
    echo '        <td>';
                      if ($checkSubmittedCode) {
                          echo '<a href = "index.php?action=downloadFile&id=' . $taskId . '&version=' . $submission['version']
          . '&file=' . urlencode($submission['class_name'] . '.java') . '" > Download</a >';
                      } else {
                          echo 'No submitted code';
                      }
    echo '        </td>';
    echo '        <td>';
                      if ($checkSubmittedCode) {
                          if ($submission['final_grade'] != null && $submission['release_grade'] == 1 && $submission['final'] == 1) {
                              echo '<a href="index.php?action=downloadFile&id=' . $taskId . '&version=' . $submission['version']
                                   . '&final=1' . '&file=' . urlencode($submission['class_name'] . '.log') . '">Download</a>';
                          } elseif ($submission['partial_grade'] != null) {
                              echo '<a href="index.php?action=downloadFile&id=' . $taskId . '&version=' . $submission['version']
                                   . '&final=0' . '&file=' . urlencode($submission['class_name'] . '.log') . '">Download</a>';
                          } else {
                              echo '';
                          }
                      } else {
                          echo 'No submitted code';
                      }
    echo '        </td>';
    echo '        <td>' . isFinal($submission['final']) . '</td>';
    echo '        <td>';
                      if ($submission['final'] == 0) {
                          if ($submission['due_date'] >= date("Y-m-d H:i:s", time())) {
                              echo '<a href="index.php?action=setFinal&id=' . $submission['id'] . '">Set this one</a>';
                          }
                      } else {
                          if ($submission['due_date'] < date("Y-m-d H:i:s", time())) {
                              echo 'Final';
                          }
                      }
    echo '        </td>';
    echo '        <td><a href="index.php?action=viewTestOutput&id=' . $submission['id'] . '">View</a></td>';
    echo '    </tr>';
}
echo '</table>';


echo '<br>';
echo '<sup>1</sup>Note: this is not the final mark for the task';
echo '<br>';
echo '<br>';
echo '<a href="index.php?action=studentTasks&id=' . $assignmentId . '">Back to Tasks</a>';