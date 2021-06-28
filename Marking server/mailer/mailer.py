from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from settings import Settings
import smtplib
import ssl


class Mailer:
    def __init__(self, host, port, email, password):
        self.host = host
        self.port = port
        self.sender = email
        self.password = password

    def send_email(self, to, subject='', html='', text=''):
        # Create a multipart message and set headers
        message = MIMEMultipart('alternative')
        message['From'] = f'Just Mark It <{self.sender}>'
        message['To'] = to
        message['Subject'] = subject

        # Add body to email
        message.attach(MIMEText(text, 'plain'))
        message.attach(MIMEText(html, 'html'))

        # Log in to server using secure context and send email
        context = ssl.create_default_context()
        with smtplib.SMTP_SSL(self.host, self.port, context=context) as smtp:
            smtp.login(self.sender, self.password)
            smtp.sendmail(self.sender, to, message.as_string())


def run_example():
    settings = Settings('../settings/settings.ini')

    base_url = settings.base_url
    mailer = Mailer(settings.email_host, settings.email_port, settings.email_from, settings.email_password)

    to = settings.email_to
    name = 'John'

    subject = 'Just Mark It - Test'

    html = ''
    html += f'Dear {name},'
    html += '<br><br>'
    html += 'This is a test email from <a href="base_url">Just Mark It</a>.'
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
    text += 'This is a test email from Just Mark It (base_url).'
    text += '\n\n'
    text += 'To reset your password, go to base_url .'
    text += '\n\n'
    text += 'Best,'
    text += '\n'
    text += 'Just Mark It'
    text += '\n'
    text += 'base_url'

    html = html.replace('base_url', base_url)
    text = text.replace('base_url', base_url)

    mailer.send_email(to, subject, html, text)


if '__main__' == __name__:
    run_example()
