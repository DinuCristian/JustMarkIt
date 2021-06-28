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

$publishDate = '';
if (isset($_SESSION['publishDate'])) {
    $publishDate = $_SESSION['publishDate'];
    unset($_SESSION['publishDate']);
}

$dueDate = '';
if (isset($_SESSION['dueDate'])) {
    $dueDate = $_SESSION['dueDate'];
    unset($_SESSION['dueDate']);
}

$assignmentPercentage = '';
if (isset($_SESSION['assignmentPercentage'])) {
    $assignmentPercentage = $_SESSION['assignmentPercentage'];
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
echo '<a href="index.php?action=viewAssignments&id=' . $courseId . '">Assignment: ' . $assignmentTitle . '</a>';
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
    echo '            <td><input type="hidden" name="action" value="editAssignment" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="go" value="true" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td></td>';
    echo '            <td><input type="hidden" name="id" value="' . $assignmentId . '" size="35"></td>';
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
    echo '            <td><label id="publishDateLabel">Publish Date*</label></td>';
    echo '            <td><input type="datetime-local" name="publishDate" required
                            value="' . strftime('%Y-%m-%dT%H:%M', strtotime($publishDate)) . '" 
                            size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="dueDateLabel">Due Date*</label></td>';
    echo '            <td><input type="datetime-local" name="dueDate" required
                            value="' . strftime('%Y-%m-%dT%H:%M', strtotime($dueDate)) . '" size="35"></td>';
    echo '        </tr>';
    echo '        <tr>';
    echo '            <td><label id="assignmentPercentageLabel">Assignment Percentage*</label></td>';
    echo '            <td><input type="text" id="assignmentPercentage" name="assignmentPercentage" value="' . $assignmentPercentage . '" size="35" required></td>';
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