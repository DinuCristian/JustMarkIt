from ftp.ftp_client import FtpClient
from datetime import datetime, timedelta
import os
import shutil


class Helper:
    @staticmethod
    def initialize_file_system_example(source_path, destination_path, file):
        print(f'=== {file} ===')

        for destination_file in os.listdir(destination_path):
            if '.gitignore' == destination_file:
                continue
            os.remove(os.path.join(destination_path, destination_file))

        source_file = os.path.join(source_path, f'{file}.java')
        if os.path.exists(source_file):
            shutil.copy(source_file, os.path.join(destination_path, f'{file}.java'))

    @staticmethod
    def finalize_file_system_example(source_path, destination_path, file):
        source_file = os.path.join(source_path, f'{file}.log')
        if os.path.exists(source_file):
            shutil.move(source_file, os.path.join(destination_path, f'{file}.log'))

        source_file = os.path.join(source_path, f'{file}.java')
        if os.path.exists(source_file):
            os.remove(source_file)

        source_file = os.path.join(source_path, f'{file}.class')
        if os.path.exists(source_file):
            os.remove(source_file)

        print('=' * (len(file) + 8))
        print()

    @staticmethod
    def initialize_file_system(settings, source_path, destination_path, class_name):
        print(f'=== {class_name} ===')

        for destination_file in os.listdir(destination_path):
            if '.gitignore' == destination_file:
                continue
            os.remove(os.path.join(destination_path, destination_file))

        with FtpClient(settings.ftp_host, settings.ftp_user, settings.ftp_password, settings.ftp_port) as ftp_client:
            source_file = os.path.join(source_path, f'{class_name}.java')
            destination_file = os.path.join(destination_path, f'{class_name}.java')
            ftp_client.download_file(source_file, destination_file)

    @staticmethod
    def finalize_file_system(settings, source_path, destination_path, class_name, create_final_directory=False):
        source_file = os.path.join(source_path, f'{class_name}.log')
        if os.path.exists(source_file):
            with FtpClient(settings.ftp_host, settings.ftp_user, settings.ftp_password,
                           settings.ftp_port) as ftp_client:
                if create_final_directory:
                    final_destination_path = os.path.join(destination_path, 'final')
                    ftp_client.mkdir(final_destination_path)
                    destination_file = os.path.join(final_destination_path, f'{class_name}.log')
                else:
                    destination_file = os.path.join(destination_path, f'{class_name}.log')
                ftp_client.upload_file(source_file, destination_file)

        source_file = os.path.join(source_path, f'{class_name}.log')
        if os.path.exists(source_file):
            os.remove(source_file)

        source_file = os.path.join(source_path, f'{class_name}.java')
        if os.path.exists(source_file):
            os.remove(source_file)

        source_file = os.path.join(source_path, f'{class_name}.class')
        if os.path.exists(source_file):
            os.remove(source_file)

        print('=' * (len(class_name) + 8))
        print()

    @staticmethod
    def current_date_time():
        return (datetime.now() + timedelta(hours=2)).strftime('%Y-%m-%d %H:%M:%S')
