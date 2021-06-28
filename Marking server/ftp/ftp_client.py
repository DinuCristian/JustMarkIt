from ftplib import FTP_TLS
from settings import Settings


class FtpClient:
    def __init__(self, host, user, password, port=21, debug_level=0):
        self.host = host
        self.user = user
        self.password = password
        self.port = int(port)
        self.debug_level = debug_level

        self.ftp = None

    def __del__(self):
        self.disconnect()

    def __enter__(self):
        self.connect()
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.disconnect()

    def connect(self):
        self.ftp = FTP_TLS()
        self.ftp.set_debuglevel(self.debug_level)

        self.ftp.connect(self.host, self.port)
        self.ftp.sendcmd(f'USER {self.user}')
        self.ftp.sendcmd(f'PASS {self.password}')

    def disconnect(self):
        if self.ftp:
            self.ftp.quit()
            self.ftp = None

    def pwd(self):
        return self.ftp.pwd()

    def ls(self):
        return self.ftp.nlst()

    def cd(self, destination):
        self.ftp.cwd(destination)

    def mkdir(self, directory):
        parts = directory.split('/')

        levels = 0
        for part in parts:
            if part not in self.ls():
                self.ftp.mkd(part)
            self.cd(part)
            levels += 1

        for level in range(levels):
            self.cd('..')

    def rm(self, directory):
        self.ftp.rm(directory)

    def upload_file(self, source, destination):
        f = open(source, 'rb')
        self.ftp.storbinary(f'STOR {destination}', f)
        f.close()

    def download_file(self, source, destination):
        f = open(destination, 'wb')
        self.ftp.retrbinary(f'RETR {source}', f.write)
        f.close()


def run_timeout_example():
    settings = Settings('../settings/settings.ini')

    ftp_client = FtpClient(settings.ftp_host, settings.ftp_user, settings.ftp_password, settings.ftp_port)
    ftp_client.connect()

    print('>ls')
    print(ftp_client.ls())
    print()

    ftp_client.disconnect()

    import time
    time.sleep(15 * 60 + 10)

    with FtpClient(settings.ftp_host, settings.ftp_user, settings.ftp_password, settings.ftp_port) as ftp_client:
        print('>ls')
        print(ftp_client.ls())
        print()


def run_example():
    settings = Settings('../settings/settings.ini')
    with FtpClient(settings.ftp_host, settings.ftp_user, settings.ftp_password, settings.ftp_port) as ftp_client:
        print('>pwd')
        print(ftp_client.pwd())
        print()

        print('>ls')
        print(ftp_client.ls())
        print()

        destination = 'test'
        print(f'>cd {destination}')
        ftp_client.cd(destination)
        print()

        print('>pwd')
        print(ftp_client.pwd())
        print()

        print('>ls')
        print(ftp_client.ls())
        print()

        directory = 'x/y/z'
        print(f'>mkd {directory}')
        print(ftp_client.mkdir(directory))
        print()

        print('>pwd')
        print(ftp_client.pwd())
        print()

        print('>ls')
        print(ftp_client.ls())
        print()

        source = '../file_system/test/upload.txt'
        destination = 'x/y/z/upload.txt'
        print(f'>upload {source} {destination}')
        ftp_client.upload_file(source, destination)
        print()

        source = 'index.html'
        destination = '../file_system/test/index.html'
        print(f'>download {source} {destination}')
        ftp_client.download_file(source, destination)
        print()

        print('>ls')
        print(ftp_client.ls())
        print()


if __name__ == '__main__':
    # run_timeout_example()
    run_example()
