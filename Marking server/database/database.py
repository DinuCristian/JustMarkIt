import mysql.connector
from settings import Settings


class Database:
    def __init__(self, host, user, password, database):
        self.host = host
        self.user = user
        self.password = password
        self.database = database

        self.connection = None

    def __del__(self):
        self.disconnect()

    def __enter__(self):
        self.connect()
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.disconnect()

    def connect(self):
        self.connection = mysql.connector.connect(host=self.host, user=self.user, password=self.password,
                                                  database=self.database)

    def disconnect(self):
        if self.connection:
            self.connection.close()
            self.connection = None

    def get_submissions(self):
        query = 'SELECT submission.id, course_id, assignment_id, task_id, user_id, version, class_name ' \
                'FROM submission ' \
                'INNER JOIN task ON submission.task_id = task.id ' \
                'INNER JOIN assignment ON task.assignment_id = assignment.id ' \
                'WHERE marking_server_professor = 1 AND marking_server_student = 1 AND submitted = 1 AND final = 1'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0], result[1], result[2], result[3], result[4], result[5], result[6])]
        return array

    def get_submission_course_assignment_id(self):
        query = 'SELECT course_id, assignment_id ' \
                'FROM submission ' \
                'INNER JOIN task ON submission.task_id = task.id ' \
                'INNER JOIN assignment ON task.assignment_id = assignment.id ' \
                'WHERE marking_server_professor = 1 AND marking_server_student = 1 AND submitted = 1 ' \
                'GROUP BY task.id'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array = [(result[0], result[1])]
        return array

    def get_submissions_student(self):
        query = 'SELECT submission.id, course_id, assignment_id, task_id, user_id, version, class_name ' \
                'FROM submission ' \
                'INNER JOIN task ON submission.task_id = task.id ' \
                'INNER JOIN assignment ON task.assignment_id = assignment.id ' \
                'WHERE marking_server_student = 0 AND submitted = 1'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0], result[1], result[2], result[3], result[4], result[5], result[6])]
        return array

    def get_tests(self, task_id):
        query = f'SELECT id, input_test, output_test, grade, test_type_id, coding_standard_id ' \
                f'FROM test ' \
                f'WHERE task_id = {task_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        tests = []
        for result in results:
            tests += [(result[0], result[1], result[2], result[3], result[4], result[5])]
        return tests

    def get_tests_student(self, task_id):
        query = f'SELECT id, input_test, output_test, grade, coding_standard_id ' \
                f'FROM test ' \
                f'WHERE test_type_id = 1 AND task_id = {task_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        tests = []
        for result in results:
            tests += [(result[0], result[1], result[2], result[3], result[4])]
        return tests

    def insert_submission_output(self, submission_id, test_id, status, output):
        query = f'INSERT INTO output (status, output, submission_id, test_id) ' \
                f'VALUES ({status}, "{output}", {submission_id}, {test_id})'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def update_submission_output(self, submission_id, test_id, status, output):
        query = f'UPDATE output ' \
                f'SET status = {status}, output = "{output}" ' \
                f'WHERE submission_id = {submission_id} AND test_id = {test_id}'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def get_submission_output(self, submission_id, test_id):
        query = f'SELECT COUNT(submission_id) ' \
                f'FROM output ' \
                f'WHERE submission_id = {submission_id} AND test_id = {test_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        return results[0][0] == 1

    def set_task_grade(self, task_id, user_id, version, grade):
        query = f'UPDATE submission ' \
                f'SET final_grade = {grade}, marking_server_professor = 0 ' \
                f'WHERE task_id = {task_id} AND user_id = {user_id} AND version = {version}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def set_task_grade_student(self, task_id, user_id, version, grade):
        query = f'UPDATE submission ' \
                f'SET partial_grade = {grade}, marking_server_student = 1 ' \
                f'WHERE task_id = {task_id} AND user_id = {user_id} AND version = {version}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def get_task_student_details(self, submission_id, user_id):
        query = f'SELECT user.name, user.email, task.title, assignment.title, course.title ' \
                f'FROM user ' \
                f'INNER JOIN course_users ON course_users.user_id = user.id ' \
                f'INNER JOIN course ON course.id = course_users.course_id ' \
                f'INNER JOIN assignment ON assignment.course_id = course.id ' \
                f'INNER JOIN task ON task.assignment_id = assignment.id ' \
                f'INNER JOIN submission ON submission.task_id = task.id ' \
                f'WHERE submission.id = {submission_id} AND user.id = {user_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        return results[0][0], results[0][1], results[0][2], results[0][3], results[0][4]

    def get_submission_professor_details(self, course_id, assignment_id):
        query = f'SELECT user.name, user.email, assignment.title, course.title, assignment.release_grade_date ' \
                f'FROM user ' \
                f'INNER JOIN course ON course.leader = user.id ' \
                f'INNER JOIN assignment ON assignment.course_id = course.id ' \
                f'WHERE course.id = {course_id} AND assignment.id = {assignment_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        return results[0][0], results[0][1], results[0][2], results[0][3], results[0][4]

    def get_assignments_with_past_due_date(self, current_date_time):
        query = f'SELECT id ' \
                f'FROM assignment ' \
                f'WHERE due_date < \'{current_date_time}\' AND due_date_notification = 0'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0])]
        return array

    def update_assignment_with_past_due_date(self, assignment_id):
        query = f'UPDATE assignment ' \
                f'SET due_date_notification = 1 ' \
                f'WHERE id = {assignment_id}'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def get_assignment_professor_details(self, assignment_id):
        query = f'SELECT user.name, user.email, assignment.title, course.title, assignment.release_grade_date ' \
                f'FROM user ' \
                f'INNER JOIN course ON course.leader = user.id ' \
                f'INNER JOIN assignment ON assignment.course_id = course.id ' \
                f'WHERE assignment.id = {assignment_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        return results[0][0], results[0][1], results[0][2], results[0][3], results[0][4]

    def get_assignments_with_past_release_date(self, current_date_time):
        query = f'SELECT id ' \
                f'FROM assignment ' \
                f'WHERE DATE_ADD(release_grade_date, INTERVAL \'23:59\' HOUR_MINUTE) < \'{current_date_time}\' ' \
                f'AND release_grade = 0'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0])]
        return array

    def update_release_grade(self, assignment_id):
        query = f'UPDATE assignment ' \
                f'SET release_grade = 1 ' \
                f'WHERE id = {assignment_id}'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def get_enrolled_students(self, assignment_id):
        query = f'SELECT course_users.user_id ' \
                f'FROM course_users ' \
                f'INNER JOIN course ON course.id = course_users.course_id ' \
                f'INNER JOIN assignment ON assignment.course_id = course.id ' \
                f'WHERE assignment.id = {assignment_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0])]
        return array

    def get_assignment_student_details(self, assignment_id, student_id):
        query = f'SELECT user.name, user.email, assignment.title, course.title ' \
                f'FROM user ' \
                f'INNER JOIN course_users ON course_users.user_id = user.id ' \
                f'INNER JOIN course ON course_users.course_id = course.id ' \
                f'INNER JOIN assignment ON assignment.course_id = course.id ' \
                f'WHERE assignment.id = {assignment_id} AND user.id = {student_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        return results[0][0], results[0][1], results[0][2], results[0][3]

    def get_release_date_submissions(self, current_date_time):
        query = f'SELECT submission.id, course_id, assignment_id, task_id, user_id, version, class_name ' \
                f'FROM submission ' \
                f'INNER JOIN task ON submission.task_id = task.id ' \
                f'INNER JOIN assignment ON task.assignment_id = assignment.id ' \
                f'WHERE marking_server_professor = 0 AND marking_server_student = 1 AND submitted = 1 AND ' \
                f'submission.final_grade IS NULL AND release_grade_date < \'{current_date_time}\' AND submission.final = 1'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0], result[1], result[2], result[3], result[4], result[5], result[6])]
        return array

    def get_release_date_assignments_ids(self, current_date_time):
        query = f'SELECT course_id, assignment_id, task.id ' \
                f'FROM task ' \
                f'INNER JOIN assignment ON task.assignment_id = assignment.id ' \
                f'INNER JOIN course ON assignment.course_id = course.id ' \
                f'WHERE release_grade_date < \'{current_date_time}\' AND active = 0'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0], result[1], result[2])]
        return array

    def get_students_no_submission(self, course_id, task_id):
        query = f'SELECT course_users.user_id ' \
                f'FROM course_users ' \
                f'INNER JOIN user ON course_users.user_id = user.id ' \
                f'WHERE course_users.course_id = {course_id} ' \
                f'AND course_users.user_id NOT IN (SELECT submission.user_id ' \
                                                 f'FROM submission ' \
                                                 f'WHERE submission.task_id = {task_id})'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0])]
        return array

    def get_last_inserted_submission_with_no_code(self):
        query = 'SELECT id ' \
                'FROM submission ' \
                'WHERE submitted = 0 ' \
                'ORDER BY id DESC LIMIT 1'
        cursor = self.connection.cursor()
        cursor.execute(query)
        result = cursor.fetchall()
        return result[0][0]

    def get_task_tests_id(self, task_id):
        query = f'SELECT id ' \
                f'FROM test ' \
                f'WHERE task_id = {task_id}'
        cursor = self.connection.cursor()
        cursor.execute(query)
        results = cursor.fetchall()
        array = []
        for result in results:
            array += [(result[0])]
        return array

    def insert_no_submission_output(self, submission_id, test_id):
        query = f'INSERT INTO output (status, output, submission_id, test_id) ' \
                f'VALUES (\'0\', \'No submission\', {submission_id}, {test_id})'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def insert_no_submission_submission(self, student_id, task_id):
        query = f'INSERT INTO submission (user_id, task_id, version, final, final_grade, submitted) ' \
                f'VALUES ({student_id}, {task_id}, 1, 1, 0, 0)'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1

    def update_assignment_active(self, assignment_id):
        query = f'UPDATE assignment ' \
                f'SET active = 1 ' \
                f'WHERE id = {assignment_id}'
        print(query)
        cursor = self.connection.cursor()
        cursor.execute(query)
        self.connection.commit()
        result = cursor.rowcount
        return result == 1


def run_example():
    settings = Settings('../settings/settings.ini')

    database = Database(settings.db_host, settings.db_user, settings.db_password, settings.db_database)
    # print(database.get_submissions())
    # print(database.get_tests(1))
    print(database.set_task_grade(1, 3, 1, 40))


if '__main__' == __name__:
    run_example()
