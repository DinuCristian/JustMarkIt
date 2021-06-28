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

$testId = 0;
if (isset($_SESSION['testId'])) {
    $testId = $_SESSION['testId'];
}

$description = '';
if (isset($_SESSION['description'])) {
    $description = $_SESSION['description'];
    unset($_SESSION['description']);
}

$inputTest = '';
if (isset($_SESSION['inputTest'])) {
    $inputTest = $_SESSION['inputTest'];
    unset($_SESSION['inputTest']);
}

$outputTest = '';
if (isset($_SESSION['outputTest'])) {
    $outputTest = $_SESSION['outputTest'];
    unset($_SESSION['outputTest']);
}

$grade = '';
if (isset($_SESSION['grade'])) {
    $grade = $_SESSION['grade'];
    unset($_SESSION['grade']);
}

$visible = '';
if (isset($_SESSION['visible'])) {
    $visible = $_SESSION['visible'];
    unset($_SESSION['visible']);
}

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

function type ($value) {
    if ($value === 'public') {
        return '1';
    } elseif ($value === 'private') {
        return '2';
    }
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
    echo '    <form name="form" onsubmit="return validate()" id="form" action="" method="get">';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="action" value="editTest" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $testId . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '         <td><input type="text" id="description" name="description" value="' . $description . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="inputTestLabel">Input test*</label></td>';
    echo '            <td><textarea id="inputTest" name="inputTest" rows="7" cols="37" required>' . $inputTest . '</textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="outputTestLabel">Output test*</td>';
    echo '            <td><textarea id="outputTest" name="outputTest" rows="7" cols="37" required>' . $outputTest . '</textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="gradeLabel">Grade*</label></td>';
    echo '         <td><input type="text" id="grade" name="grade" value="' . $grade . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="visibleLabel"><label for="visible">Visible for student*</label></label></td>';
    echo '            <td>';
    echo '                <select name="visible" id="visible" required>';
    echo '                    <option value="' . type($visible) . '">' . $visible . '</option>';
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