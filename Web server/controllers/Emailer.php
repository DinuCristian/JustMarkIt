<?php

require_once('settings/db.php');

class Emailer
{
    public function sendEmail($to, $params, $from_user = 'Just Mark It', $from_email = 'noreply@justmarkit.uk')
    {
        $from_user = "=?UTF-8?B?" . base64_encode($from_user) . "?=";
        $subject = "=?UTF-8?B?" . base64_encode($params['subject']) . "?=";

        $boundary = md5(uniqid(time()));

        $headers = "From: $from_user <$from_email>";
        $headers .= "\r\n";
        $headers .= "MIME-Version: 1.0";
        $headers .= "\r\n";
        $headers .= "Content-type: multipart/alternative; boundary=\"===============" . $boundary . "==\"";
        $headers .= "\r\n";
        $headers .= "Content-Transfer-Encoding: 7bit";
        $headers .= "\r\n";

        $message = "--===============" . $boundary . "==";
        $message .= "\r\n";
        $message .= "Content-type: text/plain; charset=\"iso-8859-1\"";
        $message .= "\r\n";
        $message .= "MIME-Version: 1.0";
        $message .= "\r\n";
        $message .= "Content-Transfer-Encoding: 7bit";
        $message .= "\r\n\r\n";
        $message .= $params['text'];
        $message .= "\r\n";
        $message .= "--===============" . $boundary . "==";
        $message .= "\r\n";
        $message .= "Content-type: text/html; charset=\"iso-8859-1\"";
        $message .= "\r\n";
        $message .= "MIME-Version: 1.0";
        $message .= "\r\n";
        $message .= "Content-Transfer-Encoding: 7bit";
        $message .= "\r\n\r\n";
        $message .= $params['html'];
        $message .= "\r\n";

        return mail($to, $subject, $message, $headers);
    }

    public function requestAccountParameters($name, $role)
    {
        $subject = 'Just Mark It - Registration Notification';

        $html = '';
        $html .= 'Dear ' . $name . ',';
        $html .= '<br><br>';
        $html .= 'We received your account registration request for <i>'. $role .'</i> role. You will receive an email after your request is reviewed by our team.';
        $html .= '<br><br>';
        $html .= '<table cellspacing="0" cellpadding="0">';
        $html .= '  <tr>';
        $html .= '    <td>';
        $html .= '      <a href="' . BASE_URL . '">';
        $html .= '        <img src="' . BASE_URL . '/images/logo.png" style="width:25px; height:25px;">';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '    <td style="vertical-align:center; font-size:20px;">';
        $html .= '      <a href="' . BASE_URL . '" style="text-decoration:none; color:#003976;">';
        $html .= '        Just Mark It';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '  </tr>';
        $html .= '</table>';

        $text = '';
        $text .= 'Dear ' . $name . ',';
        $text .= '\n\n';
        $text .= 'We received your account registration request for *'. $role .'* role. You will receive an email after your request is reviewed by our team.';
        $text .= '\n\n';
        $text .= 'Best,';
        $text .= '\n';
        $text .= 'Just Mark It';
        $text .= '\n';
        $text .= BASE_URL;
        $text .= '\n';

        $params = array(
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        );

        return $params;
    }

    public function notifyAdmin($name)
    {
        $subject = 'Just Mark It - Registration Notification Administrator';

        $html = '';
        $html .= 'Dear administrator,';
        $html .= '<br><br>';
        $html .= 'We received an administrator account registration request from ' . $name . '.';
        $html .= '<br><br>';
        $html .= '<table cellspacing="0" cellpadding="0">';
        $html .= '  <tr>';
        $html .= '    <td>';
        $html .= '      <a href="' . BASE_URL . '">';
        $html .= '        <img src="' . BASE_URL . '/images/logo.png" style="width:25px; height:25px;">';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '    <td style="vertical-align:center; font-size:20px;">';
        $html .= '      <a href="' . BASE_URL . '" style="text-decoration:none; color:#003976;">';
        $html .= '        Just Mark It';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '  </tr>';
        $html .= '</table>';

        $text = '';
        $text .= 'Dear administrator,';
        $text .= '\n\n';
        $text .= 'We received an administrator account registration request from ' . $name . '.';
        $text .= '\n\n';
        $text .= 'Best,';
        $text .= '\n';
        $text .= 'Just Mark It';
        $text .= '\n';
        $text .= BASE_URL;
        $text .= '\n';

        $params = array(
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        );

        return $params;
    }

    public function requestAccountDecision($name, $role, $decision = ' ')
    {
        $subject = 'Just Mark It - Registration Notification Decision';

        $html = '';
        $html .= 'Dear ' . $name . ',';
        $html .= '<br><br>';
        $html .= 'Your registration request has been ' . $decision . ' for <i>' . $role . '</i> role.';
        if ($decision == 'accepted') {
            $html .= 'You can ' . '<a href="'. BASE_URL .'/index.php?action=login">click here</a>' . ' to log in.';
        }
        $html .= '<br><br>';
        $html .= '<table cellspacing="0" cellpadding="0">';
        $html .= '  <tr>';
        $html .= '    <td>';
        $html .= '      <a href="' . BASE_URL . '">';
        $html .= '        <img src="' . BASE_URL . '/images/logo.png" style="width:25px; height:25px;">';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '    <td style="vertical-align:center; font-size:20px;">';
        $html .= '      <a href="' . BASE_URL . '" style="text-decoration:none; color:#003976;">';
        $html .= '        Just Mark It';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '  </tr>';
        $html .= '</table>';

        $text = '';
        $text .= 'Dear ' . $name . ',';
        $text .= '\n\n';
        $text .= 'Your registration request has been ' . $decision . ' for *' . $role . '* role.';
        if ($decision == 'accepted') {
            $text .= 'You can ' . ''. BASE_URL .'/index.php?action=login' . ' to log in.';
        }
        $text .= '\n\n';
        $text .= 'Best,';
        $text .= '\n';
        $text .= 'Just Mark It';
        $text .= '\n';
        $text .= BASE_URL;
        $text .= '\n';

        $params = array(
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        );

        return $params;
    }

    public function bulkEnrollment($name, $password)
    {
        $subject = 'Just Mark It - Enrollment';

        $html = '';
        $html .= 'Dear ' . $name . ',';
        $html .= '<br><br>';
        $html .= 'Your account has been created. You can login with your email and password = \'' . $password . '\'.';
        $html .= '<br><br>';
        $html .= 'Please ' . '<a href="'. BASE_URL .'/index.php?action=editAccountDetails">click here</a>' . ' to change your password.';
        $html .= '<br><br>';
        $html .= '<table cellspacing="0" cellpadding="0">';
        $html .= '  <tr>';
        $html .= '    <td>';
        $html .= '      <a href="' . BASE_URL . '">';
        $html .= '        <img src="' . BASE_URL . '/images/logo.png" style="width:25px; height:25px;">';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '    <td style="vertical-align:center; font-size:20px;">';
        $html .= '      <a href="' . BASE_URL . '" style="text-decoration:none; color:#003976;">';
        $html .= '        Just Mark It';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '  </tr>';
        $html .= '</table>';

        $text = '';
        $text .= 'Dear ' . $name . ',';
        $text .= '\n\n';
        $text .= 'Your account has been created. You can login with your email and password = \'' . $password . '\'';
        $text .= '\n\n';
        $text .= 'Please go to ' . ''. BASE_URL .'/index.php?action=editAccountDetails' . ' to change your password.';
        $text .= '\n\n';
        $text .= 'Best,';
        $text .= '\n';
        $text .= 'Just Mark It';
        $text .= '\n';
        $text .= BASE_URL;
        $text .= '\n';

        $params = array(
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        );

        return $params;
    }

    public function releaseGrades($name, $courseTitle, $assignmentTitle, $assignmentId )
    {
        $subject = 'Just Mark It - Registration Grades';

        $html = '';
        $html .= 'Dear ' . $name . ',';
        $html .= '<br><br>';
        $html .= 'Grades for assignment <i>'. $assignmentTitle .'</i>, course <i>'. $courseTitle .'</i> have been released';
        $html .= '<br><br>';
        $html .= 'To view the assignment report, ' . '<a href="'. BASE_URL .'/index.php?action=studentViewReports&id='. $assignmentId .'">click here</a>' . '.';
        $html .= '<br><br>';
        $html .= '<table cellspacing="0" cellpadding="0">';
        $html .= '  <tr>';
        $html .= '    <td>';
        $html .= '      <a href="' . BASE_URL . '">';
        $html .= '        <img src="' . BASE_URL . '/images/logo.png" style="width:25px; height:25px;">';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '    <td style="vertical-align:center; font-size:20px;">';
        $html .= '      <a href="' . BASE_URL . '" style="text-decoration:none; color:#003976;">';
        $html .= '        Just Mark It';
        $html .= '      </a>';
        $html .= '    </td>';
        $html .= '  </tr>';
        $html .= '</table>';

        $text = '';
        $text .= 'Dear ' . $name . ',';
        $text .= '\n\n';
        $text .= 'Grades for assignment **'. $assignmentTitle .'**, course **'. $courseTitle .'** have been released';
        $text .= '\n\n';
        $text .= 'Please go to ' . ''. BASE_URL .'/index.php?action=studentViewReports&id='. $assignmentId .'' . ' to view the assignment report.';
        $text .= '\n\n';
        $text .= 'Best,';
        $text .= '\n';
        $text .= 'Just Mark It';
        $text .= '\n';
        $text .= BASE_URL;
        $text .= '\n';

        $params = array(
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        );

        return $params;
    }
}