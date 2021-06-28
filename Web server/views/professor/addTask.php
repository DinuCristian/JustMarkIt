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

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
    unset($_SESSION['assignmentId']);
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

echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a> » ';
echo '<a href="index.php?action=viewTasks&id=' . $assignmentId . '">Tasks</a>';
echo '<br>';
echo '<br>';


echo '<div id="error"><p id="message" style="color: red;"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table class="addCourse-table">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=addTask&id=' . $assignmentId . '&go=true" method="post">';
    echo '        <tr>';
    echo '            <td><label id="titleLabel">Title*</label></td>';
    echo '         <td><input type="text" id="title" name="title" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '            <td><textarea id="description" name="description" rows="7" cols="37" required></textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="classNameLabel">Java class name*</label></td>';
    echo '         <td><input type="text" id="className" name="className" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="noSubmissionsLabel">No of submissions*</label></td>';
    echo '         <td><input type="text" id="try" name="try" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="taskPercentageLabel">Task Percentage*</label></td>';
    echo '         <td><input type="text" id="taskPercentage" name="taskPercentage" size="35" required></td>';
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
