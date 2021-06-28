<script>
    function clearFields(){
        clearMessage();

        clearLabel('titleLabel');
        clearLabel('descriptionLabel');
        clearLabel('classNameLabel');
        clearLabel('noSubmissionsLabel');
        clearLabel('taskPercentageLabel');
    }

    function validate() {
        var title = document.forms['form']['title'];
        var description = document.forms['form']['description'];
        var className = document.forms['form']['className'];
        var noSubmissions = document.forms['form']['try'];
        var taskPercentage = document.forms['form']['taskPercentage'];
        elements = [title, description, className, noSubmissions, taskPercentage];

        clearFields();
        elements.forEach(function (item, index) {
            item.addEventListener("invalid", clearFields, false);
        })

        result = requiredField('titleLabel', title, 'title');
        if (!result) {
            return false;
        }

        result = requiredField('descriptionLabel', description, 'description');
        if (!result) {
            return false;
        }

        result = requiredField('classNameLabel', className, 'class name');
        if (!result) {
            return false;
        }

        result = requiredField('noSubmissionsLabel', noSubmissions, 'try');
        if (!result) {
            return false;
        }

        result = valid2DigitsNumber('noSubmissionsLabel', noSubmissions, 'try');
        if (!result) {
            return false;
        }

        result = requiredField('taskPercentageLabel', taskPercentage, 'task percentage');
        if (!result) {
            return false;
        }

        result = validPercentage('taskPercentageLabel', taskPercentage, 'task percentage');
        if (!result) {
            document.getElementById('taskPercentageLabel').textContent += ' Integer value from 0 to 100';
            return false;
        }

        return true;
    }
</script>

<?php

$taskId = 0;
if (isset($_SESSION['taskId'])) {
    $taskId = $_SESSION['taskId'];
    unset($_SESSION['taskId']);
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
}

$title = '';
if (isset($_SESSION['title'])) {
    $title = $_SESSION['title'];
    unset($_SESSION['title']);
}

$description = '';
if (isset($_SESSION['description'])) {
    $description = $_SESSION['description'];
    unset($_SESSION['description']);
}

$className = '';
if (isset($_SESSION['className'])) {
    $className = $_SESSION['className'];
    unset($_SESSION['className']);
}

$try = '';
if (isset($_SESSION['try'])) {
    $try = $_SESSION['try'];
    unset($_SESSION['try']);
}

$taskPercentage = '';
if (isset($_SESSION['taskPercentage'])) {
    $taskPercentage = $_SESSION['taskPercentage'];
    unset($_SESSION['taskPercentage']);
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

echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a> » ';
echo '<a href="index.php?action=viewTasks&id=' . $assignmentId . '">Task: ' . $taskTitle . '</a>';
echo '<br>';
echo '<br>';


echo '<div id="error"><p id="message" style="color: red;"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table class="addCourse-table">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="" method="get">';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="action" value="editTask" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $taskId . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="titleLabel"></label>Title*</label></td>';
    echo '         <td><input type="text" id="title" name="title" value="' . $title . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '            <td><textarea id="description" name="description" rows="7" cols="37" required>' . $description . '</textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="classNameLabel">Java class name*</label></td>';
    echo '         <td><input type="text" id="className" name="className" value="' . $className . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="noSubmissionsLabel">Nr of submissions*</label></td>';
    echo '         <td><input type="text" id="try" name="try" value="' . $try . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="taskPercentageLabel">Task Percentage*</label></td>';
    echo '         <td><input type="text" id="taskPercentage" name="taskPercentage" value="' . $taskPercentage . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

    echo '<br>';
    echo '<a href="index.php?action=viewTasks&id=' . $assignmentId . '">View All Tasks</a>';
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}