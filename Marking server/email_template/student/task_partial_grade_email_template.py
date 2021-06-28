from mailer.mailer import Mailer
from settings import Settings


class StudentPartialGradeEmailTemplate:
    subject = 'Just Mark It - Assignment Partial Grade Ready Notification'

    # Parameter - Description
    # 0 - Student Name
    # 1 - Task Name
    # 2 - Assignment Name
    # 3 - Course Name
    # 4 - Submission ID
    html = ''
    html += 'Dear {0},'
    html += '<br><br>'
    html += 'Partial grade for task <i>{1}</i>, assignment <i>{2}</i>, course <i>{3}</i> is ready.'
    html += '<br><br>'
    html += 'To view your partial grade, ' \
            '<a href="base_url/index.php?action=viewTestOutput&id={4}">click here</a>.'
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
    # 0 - Student Name
    # 1 - Task Name
    # 2 - Assignment Name
    # 3 - Course Name
    # 4 - Submission ID
    text = ''
    text += 'Dear {0},'
    text += '\n\n'
    text += 'Partial grade for task **{1}**, assignment **{2}**, course **{3}** is ready.'
    text += '\n\n'
    text += 'To view the assignment report, go to ' \
            'base_url/index.php?action=viewTestOutput&id={4} .'
    text += '\n\n'
    text += 'Best,'
    text += '\n'
    text += 'Just Mark It'
    text += '\n'
    text += 'base_url'


def send_task_partial_grade_email_example():
    settings = Settings('../../settings/settings.ini')
    mailer = Mailer(settings.email_host, settings.email_port, settings.email_from, settings.email_password)

    student_name = 'Student'
    task_name = 'xTo2'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    task_id = 1

    subject = StudentPartialGradeEmailTemplate.subject
    html = StudentPartialGradeEmailTemplate.html.format(student_name, task_name, assignment_name, course_name, task_id)
    text = StudentPartialGradeEmailTemplate.text.format(student_name, task_name, assignment_name, course_name, task_id)

    html = html.replace('base_url', settings.base_url)
    text = text.replace('base_url', settings.base_url)

    mailer.send_email(settings.email_to, subject, html, text)


if '__main__' == __name__:
    send_task_partial_grade_email_example()
