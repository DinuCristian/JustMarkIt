from datetime import datetime
from mailer.mailer import Mailer
from settings import Settings
import sys
import time
import traceback
from worker.administrator_worker import AdministratorWorker
from worker.professor_worker import ProfessorWorker
from worker.student_worker import StudentWorker


class MarkingServer:
    def __init__(self):
        self.settings = Settings()
        self.mailer = Mailer(self.settings.email_host, self.settings.email_port, self.settings.email_from,
                             self.settings.email_password)

    def send_server_email(self, state='', details=''):
        if '' == self.settings.email_to:
            return

        name = 'Admin'
        subject = 'Just Mark It - Marking Server'

        html = ''
        html += f'Dear {name},'
        html += '<br><br>'
        html += f'[{datetime.now().strftime("%d/%m/%Y %H:%M:%S.%f")}] Marking server {state} ...'
        if '' != details:
            html += '<h3>Details</h3>'
            html += details
        html += '<br><br>'
        html += '<table>'
        html += '  <tr>'
        html += '    <td>'
        html += '      <a href="base_url">'
        html += '        <img src="base_url/images/logo.png" style="width:25px; height:25px;">'
        html += '      </a>'
        html += '    </td>'
        html += '    <td style="vertical-align:center; font-size:20px;">'
        html += '      <a href="base_url" style="text-decoration:none; color:#003976; ">'
        html += '        Just Mark It'
        html += '      </a>'
        html += '    </td>'
        html += '  </tr>'
        html += '</table>'

        text = ''
        text += f'Dear {name},'
        text += '\n\n'
        text += f'Marking server {state} ...'
        text += '\n\n'
        if '' != details:
            text += '**Details:**'
            text += details
            text += '\n\n'
        text += 'Best,'
        text += '\n'
        text += 'Just Mark It'
        text += '\n'
        text += 'base_url'

        html = html.replace('base_url', self.settings.base_url)
        text = text.replace('base_url', self.settings.base_url)

        self.mailer.send_email(self.settings.email_to, subject, html, text)

    def run_workers(self):
        student_worker = StudentWorker(self.settings, file_system='file_system/student')
        professor_worker = ProfessorWorker(self.settings, file_system='file_system/professor')
        administrator_worker = AdministratorWorker(self.settings, file_system='file_system/administrator')

        while True:
            student_worker.run()
            professor_worker.run()
            administrator_worker.run()
            time.sleep(10)


def run_example():
    marking_server = MarkingServer()
    marking_server.send_server_email('started')

    try:
        marking_server.run_workers()
        # raise Exception('Test feature: email on exception.')
    except Exception as e:
        stack_trace = traceback.format_exc()
        message = f'{e}; Stack Trace: {stack_trace}'
        marking_server.send_server_email('erred', message)
        print(message)
        sys.stdout.flush()
    finally:
        marking_server.send_server_email('stopped')


if '__main__' == __name__:
    run_example()
