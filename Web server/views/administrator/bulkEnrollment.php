<?php

echo 'The file must have the following columns(email, password, name, surname, end date, role) in the exact same order as in the example bellow.';
echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Email</th>';
echo '        <th>Password</th>';
echo '        <th>Name</th>';
echo '        <th>Surname</th>';
echo '        <th>End date</th>';
echo '        <th>Role</th>';
echo '    </tr>';

echo '    <tr>';
echo '        <td>example@justmarkit.uk</td>';
echo '        <td>pass</td>';
echo '        <td>Cristian</td>';
echo '        <td>Dinu</td>';
echo '        <td>01-01-2022</td>';
echo '        <td>2</td>';
echo '    </tr>';
echo '</table>';
echo '<br>';
echo '<br>';


echo 'List of roles:';
echo '<table class="list-table">';
echo '    <tr>';
echo '        <th>Role</th>';
echo '        <th>Id</th>';
echo '    </tr>';

echo '    <tr>';
echo '        <td>administrator</td>';
echo '        <td>1</td>';
echo '    </tr>';
echo '    <tr>';
echo '        <td>professor</td>';
echo '        <td>2</td>';
echo '    </tr>';
echo '    <tr>';
echo '        <td>student</td>';
echo '        <td>3</td>';
echo '    </tr>';
echo '</table>';

echo '<br>';

echo '<b>Note</b>: the file must be of type .csv';
echo '<br>';
echo '<br>';


echo '<form action="" method="post" enctype="multipart/form-data">';
echo '    Select file to upload:';
echo '    <input type="file" name="fileToUpload" id="fileToUpload">';
echo '    <input type="submit" value="Upload file" name="submit">';
echo '</form>';


echo '<br>';
echo '<a href="index.php?action=administrator">Back</a>';