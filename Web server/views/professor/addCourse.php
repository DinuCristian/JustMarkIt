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

echo '<a href="index.php?action=professor">Courses</a>';
echo '<br>';
echo '<br>';

echo '<div id="error"><p id="message" style="color: red;"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table class="addCourse-table">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=addCourse&go=true" method="post">';
    echo '        <tr>';
    echo '            <td><label id="titleLabel">Title*</label></td>';
    echo '         <td><input type="text" id="title" name="title" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '            <td><textarea id="description" name="description" rows="7" cols="37" required></textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="yearLabel">Year</label></td>';
    echo '            <td><input type="text" id="year" name="year" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="semesterLabel">Semester</label></td>';
    echo '            <td><input type="text" id="semester" name="semester" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

    echo '<br>';
    echo '<a href="index.php?action=professor">View All Courses</a>';
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}