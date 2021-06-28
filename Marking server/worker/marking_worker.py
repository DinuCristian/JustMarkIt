from enum import IntEnum
import inspect
import os
import subprocess
import sys
from worker.helper import Helper


class TestType(IntEnum):
    Public = 1
    Private = 2


class CodingStandard(IntEnum):
    NormalTest = 1
    Google = 2
    Sun = 3


class MarkingWorker:
    EXCEPTION = 'EXCEPTION'

    def __init__(self, path, file):
        self.old_path = None
        self.new_path = path
        self.file = file
        self.busy = False
        self.log_message = None

        self.enter()

    def __del__(self):
        self.exit()

    def __enter__(self):
        self.enter()
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.exit()

    def enter(self):
        if not self.busy:
            self.log_message = ''
            self.get_version()

            self.old_path = os.getcwd()
            os.chdir(self.new_path)
            self.busy = True

    def exit(self):
        if self.busy:
            f = open(f'{self.file}.log', 'w', newline='')
            f.write(self.log_message)
            f.close()

            os.chdir(self.old_path)
            self.busy = False

    def get_version(self):
        command = ['java', '--version']
        self.run_command(command)

    def compile(self):
        command = ['javac',  f'{self.file}.java']
        return self.run_command(command)

    def run_executable(self, inputs, expected_output, test_visibility=TestType.Public.value):
        command = ['java', self.file, inputs]
        return self.run_command(command, expected_output, test_visibility)

    def check_coding_standard(self, test_visibility=TestType.Public.value, coding_standard=CodingStandard.Google.value):
        if CodingStandard.Google.value == coding_standard:
            command = ['java', '-jar ../checkstyle-8.41-all.jar', '-c /google_checks.xml', f'{self.file}.java']
        elif CodingStandard.Sun == coding_standard:
            command = ['java', '-jar ../checkstyle-8.41-all.jar', '-c /sun_checks.xml', f'{self.file}.java']
        else:
            command = ['']
        return self.run_command(command, '', test_visibility, coding_standard)

    def run_command(self, command, expected_output='', test_visibility=TestType.Public.value,
                    coding_standard=CodingStandard.NormalTest.value):
        command = ' '.join(command)

        self.log_message += command
        self.log_message += '\n'
        self.log_message += '-' * len(command)
        self.log_message += '\n'

        try:
            # Timeout is working in Linux but not in Windows.
            # See python issue 30154 (https://bugs.python.org/issue30154) for more details.
            result = subprocess.run(command, shell=True, capture_output=True, timeout=60)
        except subprocess.TimeoutExpired as e:
            message = f'Just Mark It - Marking Server Exception: {e}'
            self.log_message += message
            return False, message

        output = result.stdout.decode('utf-8').rstrip()
        error = result.stderr.decode('utf-8')

        status = result.returncode == 0
        if not status:
            if CodingStandard.Sun.value == coding_standard and result.returncode == self.violations(output):
                status = True
                output = output + error
            else:
                output = error

        if not status:
            self.log_message += output
            self.log_message += error
            self.log_message += 'Error'
            self.log_message += '\n'
        else:
            if expected_output != '':
                self.log_message += f'Test visible to student: {test_visibility == TestType.Public.value}'
                self.log_message += '\n'
                self.log_message += '           Actual output: '
            self.log_message += output
            if expected_output != '':
                self.log_message += '\n'
                self.log_message += '         Expected output: ' + expected_output
                self.log_message += '\n'
                if output == expected_output or (expected_output == self.EXCEPTION and output != ''):
                    self.log_message += 'Test passed'
                else:
                    self.log_message += 'Test failed'
                    status = False
            if not self.log_message.endswith('\n'):
                self.log_message += '\n'
            if not output:
                self.log_message += 'Done'
                self.log_message += '\n'
        self.log_message += '\n'
        return status, output.replace('"', "'")

    @staticmethod
    def violations(output):
        violations = len(output.splitlines())
        if 2 <= violations:
            violations -= 2
        return violations

    @staticmethod
    def status(status):
        if status:
            return 'passed'
        else:
            return 'failed'

    def print_compile_and_check_coding_standard(self, coding_standard=CodingStandard.Google):
        result = self.compile()
        print(f'Compile: {self.status(result[0])}')
        print()

        result = self.check_coding_standard(coding_standard=coding_standard.value)
        print(f'Coding Style ({coding_standard.name}): {self.status(result[0])}; '
              f'violations: {self.violations(result[1])}')
        print()

    def print_execution_result(self, given_input, expected_output):
        result = self.run_executable(given_input, expected_output)
        print(f'          Input: {given_input}')
        print(f'Expected Output: {expected_output}')
        print(f'         Output: {result[1]}')
        print(f'         Status: {self.status(result[0])}')
        print()


def run_student01_x_to_2():
    source_path = '../examples/student01'
    destination_path = '../file_system/student'
    file = 'Xto2'

    Helper.initialize_file_system_example(source_path, destination_path, file)

    with MarkingWorker(destination_path, file) as worker:
        worker.print_compile_and_check_coding_standard()

        worker.print_execution_result('2', '4')
        worker.print_execution_result('3', '9')
        worker.print_execution_result('a', worker.EXCEPTION)
        x = 100
        worker.print_execution_result(str(x), str(x ** 2))
        x = 2147483647
        worker.print_execution_result(str(x), str(x ** 2))

    Helper.finalize_file_system_example(destination_path, source_path, file)


def run_student04_x_to_2():
    source_path = '../examples/student04'
    destination_path = '../file_system/student'
    file = 'Xto2'

    Helper.initialize_file_system_example(source_path, destination_path, file)

    worker = MarkingWorker(destination_path, file)

    worker.print_compile_and_check_coding_standard()

    if 'win32' == sys.platform:
        test_name = inspect.stack()[0][3]
        print(f'Skip this test ({test_name}) in Windows because it fails. Test it in Linux.')
    else:
        worker.print_execution_result('2', '4')
        worker.print_execution_result('3', '9')
        worker.print_execution_result('a', worker.EXCEPTION)
        x = 100
        worker.print_execution_result(str(x), str(x ** 2))
        x = 2147483647
        worker.print_execution_result(str(x), str(x ** 2))

    worker.exit()

    Helper.finalize_file_system_example(destination_path, source_path, file)


def run_example():
    run_student01_x_to_2()
    run_student04_x_to_2()


if '__main__' == __name__:
    run_example()
