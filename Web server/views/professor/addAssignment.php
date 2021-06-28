<script>
    function clearFields(){
        clearMessage();

        clearLabel('titleLabel');
        clearLabel('descriptionLabel');
        clearLabel('publishDateLabel');
        clearLabel('dueDateLabel');
        clearLabel('assignmentPercentageLabel');
    }

    function validate() {
        var title = document.forms['form']['title'];
        var description = document.forms['form']['description'];
        var publishDate = document.forms['form']['publishDate'];
        var dueDate = document.forms['form']['dueDate'];
        var assignmentPercentage = document.forms['form']['assignmentPercentage'];
        elements = [title, description, publishDate, dueDate, assignmentPercentage];

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

        result = requiredField('publishDateLabel', publishDate, 'publish date');
        if (!result) {
            return false;
        }

        result = validDateTime('publishDateLabel', publishDate);
        if (!result) {
            return false;
        }

        result = requiredField('dueDateLabel', dueDate, 'due date');
        if (!result) {
            return false;
        }

        result = validDateTime('dueDateLabel', dueDate);
        if (!result) {
            return false;
        }

        result = requiredField('assignmentPercentageLabel', assignmentPercentage, 'assignment percentage');
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

$courseTitle = '';
if (isset($_SESSION['courseTitle'])) {
    $courseTitle = $_SESSION['courseTitle'];
}

echo '<a href="index.php?action=professor">Course: ' . $courseTitle . '</a> » ';
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignments</a>';
echo '<br>';
echo '<br>';


echo '<div id="error"><p id="message" style="color: red;"></p></div>';
if (isset($_SESSION['error_message']) || !isset($_SESSION['ok_message'])) {
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red">ERROR: ' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }

    echo '<table class="addCourse-table">';
    echo '    <form name="form" onsubmit="return validate()" id="form" action="index.php?action=addAssignment&id=' . $courseId . '&go=true" method="post">';
    echo '        <tr>';
    echo '            <td><label id="titleLabel">Title*</label></td>';
    echo '            <td><input type="text" id="title" name="title" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="descriptionLabel">Description*</label></td>';
    echo '            <td><textarea id="description" name="description" rows="7" cols="37" required></textarea size="500"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="publishDateLabel">Publish Date*</label></td>';
    echo '            <td><input type="datetime-local" id="publishDate" name="publishDate" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="dueDateLabel">Due Date*</label></td>';
    echo '            <td><input type="datetime-local" id="dueDate" name="dueDate" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="assignmentPercentageLabel">Assignment Percentage*</label></td>';
    echo '         <td><input type="text" id="assignmentPercentage" name="assignmentPercentage" size="35" required></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><input type="submit" value="Submit" name="submit"></td>';
    echo '            <td><input type="reset" value="Reset" name="reset"></td>';
    echo '        </tr>';
    echo '    </form>';
    echo '</table>';

    echo '<br>';
    echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">View All Assignments</a>';
} elseif (isset($_SESSION['ok_message'])) {
    echo '<p style="color: green">' . $_SESSION['ok_message'] . '</p>';
    unset($_SESSION['ok_message']);
}
?>

<script>
    var browser = navigator.browserInfo;
    var browserName = browser[0];
    var browserVersion = browser[1];

    // Browsers that support datetime-local: https://www.w3schools.com/tags/att_input_type_datetime-local.asp
    if ((browserName === 'Chrome' && browserVersion < 20) || (browserName === 'IE' && browserVersion < 13) ||
        (browserName === 'Firefox') || (browserName === 'Safari') ||
        (browserName === 'Opera' && browserVersion < 10.1)) {
        document.getElementById('publishDateLabel').textContent = 'Publish Date (yyyy-mm-dd hh:mm)*';
        document.getElementById('dueDateLabel').textContent = 'Due Date (yyyy-mm-dd hh:mm)*';
    }
</script>
