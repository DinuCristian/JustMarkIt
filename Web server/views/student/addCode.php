<?php

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$submissions = 0;
if (isset($_SESSION['submissions'])) {
    $submissions = $_SESSION['submissions'];
    unset($_SESSION['submissions']);
}

$submissionLimit = 0;
if (isset($_SESSION['submissionLimit'])) {
    $submissionLimit = $_SESSION['submissionLimit'];
    unset($_SESSION['submissionLimit']);
}

$dueDate = '';
if (isset($_SESSION['dueDate'])) {
    $dueDate = $_SESSION['dueDate'];
    unset($_SESSION['dueDate']);
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

echo 'You have ' . ($submissionLimit - $submissions) . ' submissions left.';
echo '<br>';
if ($submissionLimit > $submissions && $dueDate > date("Y-m-d H:i:s", time())) {
    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '    Select file to upload:';
    echo '    <input type="file" name="fileToUpload" id="fileToUpload">';
    echo '    <input type="submit" value="Upload file" name="submit">';
    echo '</form>';
} else {
    echo 'Due date has passed. You can not submit code anymore.';
    echo '<br>';
}

echo '<br>';
echo '<a href="index.php?action=studentTasks&id=' . $assignmentId . '">Back to Tasks</a>';
