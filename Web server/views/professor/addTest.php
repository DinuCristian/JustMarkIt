<script>
    function clearFields(){
        clearMessage();

        clearLabel('descriptionLabel');
        clearLabel('inputTestLabel');
        clearLabel('outputTestLabel');
        clearLabel('gradeLabel');
        clearLabel('visibleLabel');
    }

    function validate() {
        var description = document.forms['form']['description'];
        var inputTest = document.forms['form']['inputTest'];
        var outputTest = document.forms['form']['outputTest'];
        var grade = document.forms['form']['grade'];
        var visible = document.forms['form']['visible'];
        elements = [description, inputTest, outputTest, grade, visible];

        clearFields();
        elements.forEach(function (item, index) {
            item.addEventListener("invalid", clearFields, false);
        })

        result = requiredField('descriptionLabel', description, 'description');
        if (!result) {
            return false;
        }

        result = requiredField('inputTestLabel', inputTest, 'input test');
        if (!result) {
            return false;
        }

        result = requiredField('outputTestLabel', outputTest, 'output test');
        if (!result) {
            return false;
        }

        result = requiredField('gradeLabel', grade, 'grade');
        if (!result) {
            return false;
        }

        result = validPercentage('gradeLabel', grade, 'grade');
        if (!result) {
            return false;
        }

        result = requiredField('visibleLabel', visible, 'visibility');
        if (!result) {
            return false;
        }

        return true;
    }
</script>

<?php

$taskId = 0;
if (isset($_SESSION['taskId'])) {
    $taskId = $_SESSION['taskId'];
}

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
}

$assignmentId = 0;
if (isset($_SESSION['assignmentId'])) {
    $assignmentId = $_SESSION['assignmentId'];
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
echo '<a href="index.php?action=viewTasks&id=' . $assignmentId . '">Task: ' . $taskTitle . '</a> » ';
echo '<a href="index.php?action=viewTests&id=' . $taskId . '">Tests</a>';
echo '<br>';
echo '<br>';


echo '<div id="error"><p id="message" style="color: red;"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table class="addCourse-table">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=addTest&id=' . $taskId . '&go=true" method="post">';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '         <td><input type="text" id="description" name="description" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="inputTestLabel">Input test*</label></td>';
    echo '            <td><textarea id="inputTest" name="inputTest" rows="7" cols="37" required></textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="outputTestLabel">Output test*</label></td>';
    echo '            <td><textarea id="outputTest" name="outputTest" rows="7" cols="37" required></textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="gradeLabel">Grade*</td>';
    echo '         <td><input type="text" id="grade" name="grade" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="visibleLabel"><label for="visible">Visible for student*</label></label></td>';
    echo '            <td>';
    echo '                <select name="visible" id="visible" required>';
    echo '                    <option value="">Select type</option>';
    echo '                    <option value="1">public</option>';
    echo '                    <option value="2">private</option>';
    echo '                </select>';
    echo '            </td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

    echo '<br>';
    echo '<a href="index.php?action=viewTests&id=' . $taskId . '">View All Tests</a>';
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}