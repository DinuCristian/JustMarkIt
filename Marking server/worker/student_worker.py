from email_template.student.task_partial_grade_email_template import StudentPartialGradeEmailTemplate
from mailer.mailer import Mailer
from worker.helper import Helper
from worker.marking_worker import MarkingWorker, TestType, CodingStandard
from database.database import Database
import sys
import time


class StudentWorker:
    def __init__(self, settings, file_system='../file_system/student'):
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
            tests = database.get_tests_student(task_id)
        for test in tests:
            (test_id, input_test, output_test, grade, coding_standard_id) = test
            self.__log_output(f'Test: {test}; ', end='')

            if CodingStandard.NormalTest.value == coding_standard_id:
                status, output = worker.run_executable(input_test, output_test, TestType.Public.value)
                if status:
                    final_grade += grade
            else:
                status, output = worker.check_coding_standard(coding_standard=coding_standard_id)
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
                database.insert_submission_output(submission_id, test_id, status, output)

        return final_grade

    def __send_student_email(self, submission_id, user_id):
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            email_info = database.get_task_student_details(submission_id, user_id)
        (student_name, student_email, task_name, assignment_name, course_name) = email_info

        subject = StudentPartialGradeEmailTemplate.subject
        html = StudentPartialGradeEmailTemplate.html.format(student_name, task_name, assignment_name, course_name,
                                                            submission_id)
        text = StudentPartialGradeEmailTemplate.text.format(student_name, task_name, assignment_name, course_name,
                                                            submission_id)

        html = html.replace('base_url', self.settings.base_url)
        text = text.replace('base_url', self.settings.base_url)

        mailer = Mailer(self.settings.email_host, self.settings.email_port,
                        self.settings.email_from, self.settings.email_password)
        mailer.send_email(student_email, subject, html, text)

    def run(self):
        self.__log_output('student worker: started')
        with Database(self.settings.db_host, self.settings.db_user, self.settings.db_password,
                      self.settings.db_database) as database:
            submissions = database.get_submissions_student()

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
                    database.set_task_grade_student(task_id, user_id, version, final_grade)
                self.__send_student_email(submission_id, user_id)

            Helper.finalize_file_system(self.settings, self.file_system, source_path, class_name)
        self.__log_output('student worker: stopped')


def run_example():
    while True:
        worker = StudentWorker()
        worker.run()
        print('========================')
        time.sleep(10)


if __name__ == "__main__":
    run_example()
