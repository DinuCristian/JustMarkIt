import configparser
from datetime import datetime


class Settings:
    def __init__(self, settings_file='settings/settings.ini'):
        self.__mode = None

        self.__base_url = None

        self.__email_host = None
        self.__email_port = None
        self.__email_from = None
        self.__email_password = None
        self.__email_to = None

        self.__ftp_host = None
        self.__ftp_port = None
        self.__ftp_user = None
        self.__ftp_password = None
        self.__ftp_file_system = None

        self.__db_host = None
        self.__db_user = None
        self.__db_password = None
        self.__db_database = None

        self.read_config(settings_file)

    def read_config(self, settings_file):
        settings = configparser.ConfigParser()
        settings.read(settings_file)

        self.__mode = settings['Mode']['mode']
        mode_settings = settings[self.__mode]

        self.__base_url = mode_settings['base_url']

        self.__email_host = mode_settings['email_host']
        self.__email_port = mode_settings['email_port']
        self.__email_from = mode_settings['email_from']
        self.__email_password = mode_settings['email_password']
        self.__email_to = mode_settings['email_to']

        self.__ftp_host = mode_settings['ftp_host']
        self.__ftp_port = mode_settings['ftp_port']
        self.__ftp_user = mode_settings['ftp_user']
        self.__ftp_password = mode_settings['ftp_password']
        self.__ftp_file_system = mode_settings['ftp_file_system']

        self.__db_host = mode_settings['db_host']
        self.__db_user = mode_settings['db_user']
        self.__db_password = mode_settings['db_password']
        self.__db_database = mode_settings['db_database']

    def display_settings(self):
        print('Marking Server - Settings')
        print('=' * 25)
        print(f'\tDate and time: {datetime.now().strftime("%d/%m/%Y %H:%M:%S")}')
        print()

        print(f'\tMode: {self.__mode}')
        print()

        print('\tJustMarkIt')
        print(f'\t\tBase URL: {self.base_url}')
        print()

        print('\tEmail')
        print(f'\t\tHost: {self.__email_host}')
        print(f'\t\tPort: {self.__email_port}')
        print(f'\t\tFrom: {self.__email_from}')
        print(f'\t\tPassword: {self.__email_password[0]}***{self.__email_password[-1]}')
        print(f'\t\tTo: {self.__email_to}')
        print()

        print('\tFTP')
        print(f'\t\tHost: {self.__ftp_host}')
        print(f'\t\tPort: {self.__ftp_port}')
        print(f'\t\tUser: {self.__ftp_user}')
        print(f'\t\tPassword: {self.__ftp_password[0]}***{self.__ftp_password[-1]}')
        print(f'\t\tFile System: {self.__ftp_file_system}')
        print()

        print('\tDB')
        print(f'\t\tHost: {self.__db_host}')
        print(f'\t\tUser: {self.__db_user}')
        print(f'\t\tPassword: {self.__db_password[0]}***{self.__db_password[-1]}')
        print(f'\t\tDatabase: {self.__db_database}')
        print()

    @property
    def base_url(self):
        return self.__base_url

    @property
    def email_host(self):
        return self.__email_host

    @property
    def email_port(self):
        return self.__email_port

    @property
    def email_from(self):
        return self.__email_from

    @property
    def email_password(self):
        return self.__email_password

    @property
    def email_to(self):
        return self.__email_to

    @property
    def ftp_host(self):
        return self.__ftp_host

    @property
    def ftp_port(self):
        return self.__ftp_port

    @property
    def ftp_user(self):
        return self.__ftp_user

    @property
    def ftp_password(self):
        return self.__ftp_password

    @property
    def ftp_file_system(self):
        return self.__ftp_file_system

    @property
    def db_host(self):
        return self.__db_host

    @property
    def db_user(self):
        return self.__db_user

    @property
    def db_password(self):
        return self.__db_password

    @property
    def db_database(self):
        return self.__db_database


def run_example():
    settings = Settings()
    settings.display_settings()


if '__main__' == __name__:
    run_example()
