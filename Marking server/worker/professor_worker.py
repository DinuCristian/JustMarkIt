from email_template.professor.assignment_report_ready_template import ProfessorAssignmentReportReadyEmailTemplate
from mailer.mailer import Mailer
from worker.helper import Helper
from worker.marking_worker import MarkingWorker, TestType, CodingStandard
from database.database import Database
import os
import sys
import time


class ProfessorWorker:
    def __init__(self, settings, file_system='../file_system/professor'):
        self.settings = settings
        self.file_system = file_system

    @staticmethod
    def __log_output(message='', end='\n'):
        print(message, end=end)
        sys.stdout.flush()

    def __run_tests(self, worker, submission_id, task_id):
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

            self.__log_output(f'{worker.status(status)}')

            with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                          self.settings.db_database) as database:
                if TestType.Private.value == test_type_id:
                    if database.get_submission_output(submission_id, test_id):
                        database.update_submission_output(submission_id, test_id, status, output)
                    else:
                        database.insert_submission_output(submission_id, test_id, status, output)
                else:
                    database.update_submission_output(submission_id, test_id, status, output)

        return final_grade

    def __send_professor_email(self, course_id, assignment_id):
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            email_info = database.get_submission_professor_details(course_id, assignment_id)

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

    def run(self):
        self.__log_output('professor worker: started')
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            submissions = database.get_submissions()
            ids = database.get_submission_course_assignment_id()

        for submission in submissions:
            (submission_id, course_id, assignment_id, task_id, user_id, version, class_name) = submission

            source_path = f'{self.settings.ftp_file_system}/' \
                          f'{course_id}/{assignment_id}/{task_id}/{user_id}/{version}/'
            Helper.initialize_file_system(self.settings, source_path, self.file_system, class_name)

            with MarkingWorker(self.file_system, class_name) as worker:
                self.__log_output(f'Filename: {class_name}')

                worker.compile()

                final_grade = self.__run_tests(worker, submission_id, task_id)
                with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                              self.settings.db_database) as database:
                    database.set_task_grade(task_id, user_id, version, final_grade)

            Helper.finalize_file_system(self.settings, self.file_system, source_path, class_name, True)

        if ids:
            (course_id, assignment_id) = ids[0]
            self.__log_output('send email to professor')
            self.__send_professor_email(course_id, assignment_id)

        self.__log_output('professor worker: stopped')


def run_example():
    while True:
        worker = ProfessorWorker()
        worker.run()
        print('========================')
        time.sleep(10)


if __name__ == "__main__":
    run_example()
