from email_template.professor.due_date_email_template import ProfessorDueDateEmailTemplate
from email_template.professor.assignment_report_ready_template import ProfessorAssignmentReportReadyEmailTemplate
from email_template.professor.release_date_email_template import ProfessorReleaseDateEmailTemplate

from email_template.student.task_partial_grade_email_template import StudentPartialGradeEmailTemplate
from email_template.student.release_date_email_template import StudentReleaseDateEmailTemplate

from mailer.mailer import Mailer
from settings import Settings


class EmailSender:
    def __init__(self, mailer):
        self.mailer = mailer

    def send_due_date_email_to_professor(self, professor_email, professor_name, assignment_name, course_name,
                                         release_date, assignment_id, base_url):

        subject = ProfessorDueDateEmailTemplate.subject
        html = ProfessorDueDateEmailTemplate.html.format(professor_name, assignment_name, course_name, release_date,
                                                         assignment_id)
        text = ProfessorDueDateEmailTemplate.text.format(professor_name, assignment_name, course_name, release_date,
                                                         assignment_id)

        html = html.replace('base_url', base_url)
        text = text.replace('base_url', base_url)

        self.mailer.send_email(professor_email, subject, html, text)

    def send_assignment_report_ready_email_to_professor(self, professor_email, professor_name, assignment_name,
                                                        course_name, release_date, assignment_id, base_url):
        subject = ProfessorAssignmentReportReadyEmailTemplate.subject
        html = ProfessorAssignmentReportReadyEmailTemplate.html.format(professor_name, assignment_name, course_name,
                                                                       release_date, assignment_id)
        text = ProfessorAssignmentReportReadyEmailTemplate.text.format(professor_name, assignment_name, course_name,
                                                                       release_date, assignment_id)

        html = html.replace('base_url', base_url)
        text = text.replace('base_url', base_url)

        self.mailer.send_email(professor_email, subject, html, text)

    def send_release_date_email_to_professor(self, professor_email, professor_name, assignment_name, course_name,
                                             assignment_id, base_url):
        subject = ProfessorReleaseDateEmailTemplate.subject
        html = ProfessorReleaseDateEmailTemplate.html.format(professor_name, assignment_name, course_name,
                                                             assignment_id)
        text = ProfessorReleaseDateEmailTemplate.text.format(professor_name, assignment_name, course_name,
                                                             assignment_id)

        html = html.replace('base_url', base_url)
        text = text.replace('base_url', base_url)

        self.mailer.send_email(professor_email, subject=subject, html=html, text=text)

    def send_task_partial_grade_email_to_student(self, student_email, student_name, task_name, assignment_name,
                                                 course_name, task_id, base_url):
        subject = StudentPartialGradeEmailTemplate.subject
        html = StudentPartialGradeEmailTemplate.html.format(student_name, task_name, assignment_name, course_name,
                                                            task_id)
        text = StudentPartialGradeEmailTemplate.text.format(student_name, task_name, assignment_name, course_name,
                                                            task_id)

        html = html.replace('base_url', base_url)
        text = text.replace('base_url', base_url)

        self.mailer.send_email(student_email, subject, html, text)

    def send_release_date_email_to_student(self, student_email, student_name, assignment_name, course_name,
                                           assignment_id, base_url):
        subject = StudentReleaseDateEmailTemplate.subject
        html = StudentReleaseDateEmailTemplate.html.format(student_name, assignment_name, course_name, assignment_id)
        text = StudentReleaseDateEmailTemplate.text.format(student_name, assignment_name, course_name, assignment_id)

        html = html.replace('base_url', base_url)
        text = text.replace('base_url', base_url)

        self.mailer.send_email(student_email, subject, html, text)


def send_due_date_email_to_professor_example(mailer, email_to, base_url):
    professor_email = email_to
    professor_name = 'Professor'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    release_date = '1 March 2021'
    assignment_id = 1

    email_sender = EmailSender(mailer)
    email_sender.send_due_date_email_to_professor(professor_email, professor_name, assignment_name, course_name,
                                                  release_date, assignment_id, base_url)


def send_assignment_report_ready_email_to_professor_example(mailer, email_to, base_url):
    professor_email = email_to
    professor_name = 'Professor'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    release_date = '1 March 2021'
    assignment_id = 1

    email_sender = EmailSender(mailer)
    email_sender.send_assignment_report_ready_email_to_professor(professor_email, professor_name, assignment_name,
                                                                 course_name, release_date, assignment_id, base_url)


def send_release_date_email_to_professor_example(mailer, email_to, base_url):
    professor_email = email_to
    professor_name = 'Professor'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    assignment_id = 1

    email_sender = EmailSender(mailer)
    email_sender.send_release_date_email_to_professor(professor_email, professor_name, assignment_name, course_name,
                                                      assignment_id, base_url)


def send_task_partial_grade_email_to_student_example(mailer, email_to, base_url):
    student_email = email_to
    student_name = 'Student'
    task_name = 'xTo2'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    task_id = 1

    email_sender = EmailSender(mailer)
    email_sender.send_task_partial_grade_email_to_student(student_email, student_name, task_name, assignment_name,
                                                          course_name, task_id, base_url)


def send_release_date_email_to_student_example(mailer, email_to, base_url):
    student_email = email_to
    student_name = 'Student'
    assignment_name = 'Power of numbers'
    course_name = 'Introduction to programming in Java'
    assignment_id = 1

    email_sender = EmailSender(mailer)
    email_sender.send_release_date_email_to_student(student_email, student_name, assignment_name, course_name,
                                                    assignment_id, base_url)


def send_email_to_professor_examples():
    settings = Settings('../settings/settings.ini')
    mailer = Mailer(settings.email_host, settings.email_port, settings.email_from, settings.email_password)

    send_due_date_email_to_professor_example(mailer, settings.email_to, settings.base_url)
    send_assignment_report_ready_email_to_professor_example(mailer, settings.email_to, settings.base_url)
    send_release_date_email_to_professor_example(mailer, settings.email_to, settings.base_url)


def send_email_to_student_examples():
    settings = Settings('../settings/settings.ini')
    mailer = Mailer(settings.email_host, settings.email_port, settings.email_from, settings.email_password)

    send_task_partial_grade_email_to_student_example(mailer, settings.email_to, settings.base_url)
    send_release_date_email_to_student_example(mailer, settings.email_to, settings.base_url)


if '__main__' == __name__:
    send_email_to_professor_examples()
    send_email_to_student_examples()
