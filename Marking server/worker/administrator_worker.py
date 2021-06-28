from email_template.professor.due_date_email_template import ProfessorDueDateEmailTemplate
from email_template.professor.release_date_email_template import ProfessorReleaseDateEmailTemplate
from email_template.professor.assignment_report_ready_template import ProfessorAssignmentReportReadyEmailTemplate
from email_template.student.release_date_email_template import StudentReleaseDateEmailTemplate
from mailer.mailer import Mailer
from worker.helper import Helper
from worker.marking_worker import MarkingWorker, TestType, CodingStandard
from database.database import Database
import sys
import time


class AdministratorWorker:
    def __init__(self, settings, file_system='../file_system/professor'):
        self.settings = settings
        self.file_system = file_system

    @staticmethod
    def __log_output(message='', end='\n'):
        print(message, end=end)
        sys.stdout.flush()

    def __run_tests(self):
        # 2.1. Release Date 00:00 -> run tests for all submissions with final grade null and
        # marking_server_professor set to 0
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            submissions = database.get_release_date_submissions(Helper.current_date_time())

        for submission in submissions:
            (submission_id, course_id, assignment_id, task_id, user_id, version, class_name) = submission

            source_path = f'{self.settings.ftp_file_system}/' \
                          f'{course_id}/{assignment_id}/{task_id}/{user_id}/{version}/'
            Helper.initialize_file_system(self.settings, source_path, self.file_system, class_name)

            with MarkingWorker(self.file_system, class_name) as worker:
                self.__log_output(f'Filename: {class_name}')

                worker.compile()

                final_grade = 0
                with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                              self.settings.db_database) as database:
                    tests = database.get_tests(task_id)
                for test in tests:
                    (test_id, input_test, output_test, grade, test_type_id, coding_standard_id) = test
                    self.__log_output(f'Test: {test}; ', end='')

                    if CodingStandard.NormalTest.value == coding_standard_id:
                        status, output = worker.run_executable(input_test, output_test, test_type_id)
                        if status:
                            final_grade += grade
                    else:
                        status, output = worker.check_coding_standard(test_type_id, coding_standard_id)
                        violations = worker.violations(output)
                        if status:
                            accepted_violations = int(input_test)
                            if violations <= accepted_violations:
                                final_grade += grade
                            else:
                                violations -= accepted_violations
                                grade = max(0, grade - int(output_test) * violations)
                                final_grade += grade
                                if grade == 0:
                                    status = False

                    with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                                  self.settings.db_database) as database:
                        if TestType.Private.value == test_type_id:
                            if database.get_submission_output(submission_id, test_id):
                                database.update_submission_output(submission_id, test_id, status, output)
                            else:
                                database.insert_submission_output(submission_id, test_id, status, output)
                        else:
                            database.update_submission_output(submission_id, test_id, status, output)

                with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                              self.settings.db_database) as database:
                    database.set_task_grade(task_id, user_id, version, final_grade)

            Helper.finalize_file_system(self.settings, self.file_system, source_path, class_name, True)

        # 2.2. Release Date 00:00 -> students who made no submissions; set grade to 0 and output to no submission
        self.__handle_no_submission()

    def __handle_no_submission(self):
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            assignments = database.get_release_date_assignments_ids(Helper.current_date_time())

        for assignment in assignments:
            (course_id, assignment_id, task_id) = assignment
            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                student_ids = database.get_students_no_submission(course_id, task_id)
            for student_id in student_ids:
                (user_id) = student_id
                with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                              self.settings.db_database) as database:
                    database.insert_no_submission_submission(user_id, task_id)

                    result = database.get_last_inserted_submission_with_no_code()
                    submission_id = result
                    tests = database.get_task_tests_id(task_id)
                for test in tests:
                    (test_id) = test
                    with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                                  self.settings.db_database) as database:
                        database.insert_no_submission_output(submission_id, test_id)

        if assignments:
            (assignment_id) = assignments[0][1]
            self.__send_professor_assignment_report_ready_email(assignment_id)
            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                database.update_assignment_active(assignment_id)

    def __send_professor_due_date_email(self):
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            assignment_ids = database.get_assignments_with_past_due_date(Helper.current_date_time())
        for assignment_id in assignment_ids:
            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                email_info = database.get_assignment_professor_details(assignment_id)
            (professor_name, professor_email, assignment_name, course_name, release_date) = email_info
            release_date = release_date.date()

            subject = ProfessorDueDateEmailTemplate.subject
            html = ProfessorDueDateEmailTemplate.html.format(professor_name, assignment_name, course_name,
                                                             release_date, assignment_id)
            text = ProfessorDueDateEmailTemplate.text.format(professor_name, assignment_name, course_name,
                                                             release_date, assignment_id)

            html = html.replace('base_url', self.settings.base_url)
            text = text.replace('base_url', self.settings.base_url)

            mailer = Mailer(self.settings.email_host, self.settings.email_port,
                            self.settings.email_from, self.settings.email_password)
            mailer.send_email(professor_email, subject, html, text)

            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                database.update_assignment_with_past_due_date(assignment_id)

    def __send_professor_assignment_report_ready_email(self, assignment_id):
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            email_info = database.get_assignment_professor_details(assignment_id)
        (professor_name, professor_email, assignment_name, course_name, release_date) = email_info
        release_date = release_date.date()

        subject = ProfessorAssignmentReportReadyEmailTemplate.subject
        html = ProfessorAssignmentReportReadyEmailTemplate.html.format(professor_name, assignment_name,
                                                                       course_name, release_date, assignment_id)
        text = ProfessorAssignmentReportReadyEmailTemplate.text.format(professor_name, assignment_name,
                                                                       course_name, release_date, assignment_id)

        html = html.replace('base_url', self.settings.base_url)
        text = text.replace('base_url', self.settings.base_url)

        mailer = Mailer(self.settings.email_host, self.settings.email_port,
                        self.settings.email_from, self.settings.email_password)
        mailer.send_email(professor_email, subject, html, text)

    def __send_release_grade_emails(self):
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            assignment_ids = database.get_assignments_with_past_release_date(Helper.current_date_time())

        for assignment_id in assignment_ids:
            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                email_info = database.get_assignment_professor_details(assignment_id)
            (professor_name, professor_email, assignment_name, course_name, release_grade_date) = email_info

            subject = ProfessorReleaseDateEmailTemplate.subject
            html = ProfessorReleaseDateEmailTemplate.html.format(professor_name, assignment_name, course_name,
                                                                 assignment_id)
            text = ProfessorReleaseDateEmailTemplate.text.format(professor_name, assignment_name, course_name,
                                                                 assignment_id)

            html = html.replace('base_url', self.settings.base_url)
            text = text.replace('base_url', self.settings.base_url)

            mailer = Mailer(self.settings.email_host, self.settings.email_port,
                            self.settings.email_from, self.settings.email_password)
            mailer.send_email(professor_email, subject, html, text)

            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                student_ids = database.get_enrolled_students(assignment_id)
            for student_id in student_ids:
                with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                              self.settings.db_database) as database:
                    email_info = database.get_assignment_student_details(assignment_id, student_id)
                (student_name, student_email, assignment_name, course_name) = email_info

                subject = StudentReleaseDateEmailTemplate.subject
                html = StudentReleaseDateEmailTemplate.html.format(student_name, assignment_name, course_name,
                                                                   assignment_id)
                text = StudentReleaseDateEmailTemplate.text.format(student_name, assignment_name, course_name,
                                                                   assignment_id)

                html = html.replace('base_url', self.settings.base_url)
                text = text.replace('base_url', self.settings.base_url)

                mailer = Mailer(self.settings.email_host, self.settings.email_port,
                                self.settings.email_from, self.settings.email_password)
                mailer.send_email(student_email, subject, html, text)

            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                database.update_release_grade(assignment_id)

    def run(self):
        self.__log_output('administrator worker: started')
        # Timeline: Publish Date ---> Due Date ---> Release Date (Due Date + 1 week)
        # 1. Due Date -> notify professor
        # 2. Release Date 00:00 -> run tests
        # 3. Release Date 23:59 -> release grades: notify professor and students

        # 1. Due date passed: send notification email to professor and update database
        self.__send_professor_due_date_email()

        # 2. Release Date 00:00
        self.__run_tests()

        # 3. Release Date 23:59
        self.__send_release_grade_emails()
        self.__log_output('administrator worker: stopped')


def run_example():
    while True:
        worker = AdministratorWorker()
        worker.run()
        print('========================')
        time.sleep(10)


if __name__ == "__main__":
    run_example()
