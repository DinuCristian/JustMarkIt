from mailer.mailer import Mailer
from settings import Settings


class ProfessorAssignmentReportReadyEmailTemplate:
    subject = 'Just Mark It - Assignment Report Ready Notification'

    # Parameter - Description
    # 0 - Professor Name
    # 1 - Assignment Name
    # 2 - Course Name
    # 3 - Release Date
    # 4 - Assignment ID
    html = ''
    html += 'Dear {0},'
    html += '<br><br>'
    html += 'Report for assignment <i>{1}</i>, course <i>{2}</i> is ready. In case you don\'t take an action by ' \
            '<span style="color:red"><b>{3} at 23:59</b></span>, final grades will be released on ' \
            '<span style="color:red"><b>{3} at 23:59</b></span>.'
    html += '<br><br>'
    html += 'To view the assignment report and release final grades, ' \
            '<a href="base_url/index.php?action=professorViewReports&id={4}">click here</a>.'
    html += '<br><br>'
    html += '<table cellspacing="0" cellpadding="0">'
    html += '  <tr>'
    html += '    <td>'
    html += '      <a href="base_url">'
    html += '        <img src="base_url/images/logo.png" style="width:25px; height:25px;">'
    html += '      </a>'
    html += '    </td>'
    html += '    <td style="vertical-align:center; font-size:20px;">'
    html += '      <a href="base_url" style="text-decoration:none; color:#003976;">'
    html += '        Just Mark It'
    html += '      </a>'
    html += '    </td>'
    html += '  </tr>'
    html += '</table>'

    # Parameter - Description
    # 0 - Professor Name
    # 1 - Assignment Name
    # 2 - Course Name
    # 3 - Assignment ID
    text = ''
    text += 'Dear {0},'
    text += '\n\n'
    text += 'Report for assignment **{1}**, course **{2}** is ready. In case you don\'t take an action by ' \
            '*{3} at 23:59*, final grades will be released on *{3} at 23:59*.'
    text += '\n\n'
    text += 'To view the assignment report and release final grades, go to ' \
            'base_url/index.php?action=professorViewReports&id={4} .'
    text += '\n\n'
    text += 'Best,'
    text += '\n'
    text += 'Just Mark It'
    text += '\n'
    text += 'base_url'


def send_assignment_report_ready_email_example():
    settings = Settings('../../settings/settings.ini')
    mailer = Mailer(settings.email_host, settings.email_port, settings.email_from, settings.email_password)

    professor_name = 'Professor'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    release_date = '1 March 2021'
    assignment_id = 1

    subject = ProfessorAssignmentReportReadyEmailTemplate.subject
    html = ProfessorAssignmentReportReadyEmailTemplate.html.format(professor_name, assignment_name, course_name,
                                                                   release_date, assignment_id)
    text = ProfessorAssignmentReportReadyEmailTemplate.text.format(professor_name, assignment_name, course_name,
                                                                   release_date, assignment_id)

    html = html.replace('base_url', settings.base_url)
    text = text.replace('base_url', settings.base_url)

    mailer.send_email(settings.email_to, subject, html, text)


if '__main__' == __name__:
    send_assignment_report_ready_email_example()
