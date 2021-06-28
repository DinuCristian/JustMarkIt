<script>
    function clearFields(){
        clearMessage();

        clearLabel('titleLabel');
        clearLabel('descriptionLabel');
        clearLabel('yearLabel');
        clearLabel('semesterLabel');
    }

    function validate() {
        var title = document.forms['form']['title'];
        var description = document.forms['form']['description'];
        var year = document.forms['form']['year'];
        var semester = document.forms['form']['semester'];
        elements = [title, description, year, semester];

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

        result = requiredField('yearLabel', year, 'year');
        if (!result) {
            return false;
        }

        result = validYear('yearLabel', year);
        if (!result) {
            return false;
        }

        result = requiredField('semesterLabel', semester, 'semester');
        if (!result) {
            return false;
        }

        result = validSemester('semesterLabel', semester);
        if (!result) {
            return false;
        }

        return true;
    }
</script>

<?php

$courseId = 0;
if (isset($_SESSION['courseId'])) {
    $courseId = $_SESSION['courseId'];
    unset($_SESSION['courseId']);
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

$year = '';
if (isset($_SESSION['year'])) {
    $year = $_SESSION['year'];
    unset($_SESSION['year']);
}

$semester = '';
if (isset($_SESSION['semester'])) {
    $semester = $_SESSION['semester'];
    unset($_SESSION['semester']);
}

echo '<a href="index.php?action=professor">Course: ' . $title . '</a>';
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
    echo '            <td><input type="hidden" name="action" value="editCourse" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $courseId . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="titleLabel">Title*</label></td>';
    echo '            <td><input type="text" id="title" name="title" value="' . $title . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '            <td><textarea id="description" name="description" rows="7" cols="37" required>' . $description . '</textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="yearLabel">Year</label></td>';
    echo '            <td><input type="text" id="year" name="year" value="' . $year . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="semesterLabel">Semester</label></td>';
    echo '            <td><input type="text" id="semester" name="semester" value="' . $semester . '" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}

echo '<br>';
echo '<a href="index.php?action=professor">View All Courses</a>';