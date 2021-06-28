navigator.browserInfo = (function(){
    var ua = navigator.userAgent, tem,
        M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if (/trident/i.test(M[1])) {
        tem = /\brv[ :] + (\d+)/g.exec(ua) || [];
        return ['IE', (tem[1] || 0)];
    }

    if (M[1] === 'Chrome') {
        tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
        if(tem != null) {
            return tem.slice(1).join(' ').replace('OPR', 'Opera');
        }
    }

    M = M[2] ? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem = ua.match(/version\/(\d+)/i))!= null) {
        M.splice(1, 1, tem[1]);
    }

    return M;
})();

function clearLabel(labelId) {
    document.getElementById(labelId).setAttribute("style", "color: black; font-style: normal");
}

function setLabel(labelId) {
    document.getElementById(labelId).setAttribute("style", "color: red; font-style: italic");
}

function clearMessage() {
    var element = document.getElementById('message');
    element.textContent = '';
}

function setMessage(message) {
    var element = document.getElementById('message');
    element.textContent = message;
}

function requiredField(labelId, field, fieldName) {
    if (field.value === '') {
        setLabel(labelId);
        setMessage('Type the ' + fieldName + '!');
        field.focus();
        return false;
    }
    return true;
}

function validField(labelId, field, fieldName) {
    const re = /^([a-zA-Z' ]+)$/;
    if (!re.test(String(field.value).toLowerCase())) {
        setLabel(labelId);
        setMessage('Type a valid ' + fieldName + '!');
        field.focus();
        return false;
    }
    return true;
}

function validEmail(labelId, email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!re.test(String(email.value).toLowerCase())) {
        setLabel(labelId);
        setMessage('Invalid email format!');
        email.focus();
        return false;
    }
    return true;
}

function comparePasswords(labelId2, password, password2) {
    if (password.value !== password2.value) {
        setLabel(labelId2);
        setMessage('Passwords don\'t match!');
        password.focus();
        return false;
    }
    return true;
}

function validYear(labelId, year) {
    // First check for the pattern
    if (!/^\d{4}$/.test(year.value)) {
        setLabel(labelId);
        setMessage('Invalid year format!');
        year.focus();
        return false;
    }
    return true;
}

function validSemester(labelId, semester) {
    // First check for the pattern
    if (!/^\d{1}$/.test(semester.value)) {
        setLabel(labelId);
        setMessage('Invalid semester format!');
        semester.focus();
        return false;
    }
    return true;
}

function valid2DigitsNumber(labelId, number, fieldName) {
    // First check for the pattern
    if (!/^\d{1,2}$/.test(number.value)) {
        setLabel(labelId);
        setMessage('Invalid ' + fieldName + ' format!');
        number.focus();
        return false;
    }

    if (number.value < 0 || number.value > 99) {
        setLabel(labelId);
        setMessage('Invalid percentage format!');
        number.focus();
        return false;
    }
    return true;
}

function validPercentage(labelId, percentage, fieldName) {
    // First check for the pattern
    if (!/^\d{1,3}$/.test(percentage.value)) {
        setLabel(labelId);
        setMessage('Invalid ' + fieldName + ' format!');
        percentage.focus();
        return false;
    }

    if (percentage.value < 0 || percentage.value > 100) {
        setLabel(labelId);
        setMessage('Invalid percentage format!');
        percentage.focus();
        return false;
    }
    return true;
}

function validDate(labelId, date) {
    // First check for the pattern
    if(!/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(date.value)) {
        setLabel(labelId);
        setMessage('Invalid date format!');
        date.focus();
        return false;
    }

    // Parse the date parts to integers
    var parts = date.value.split("-");
    var day = parseInt(parts[2], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[0], 10);

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month === 0 || month > 12) {
        setLabel(labelId);
        setMessage('Invalid date format!');
        date.focus();
        return false;
    }

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 === 0 || (year % 100 !== 0 && year % 4 === 0))
        monthLength[1] = 29;

    // Check the range of the day
    if (day <= 0 || day > monthLength[month - 1]) {
        setLabel(labelId);
        setMessage('Invalid date format!');
        date.focus();
        return false;
    }
    return true;
}

function validDateTime(labelId, dateTime) {
    // First check for the pattern
    dateTimeValue = dateTime.value.replace(' ', 'T');
    if(!/^\d{4}\-\d{1,2}\-\d{1,2}T\d{2}:\d{2}$/.test(dateTimeValue)) {
        setLabel(labelId);
        setMessage('Invalid date and time format!');
        dateTime.focus();
        return false;
    }

    // Parse the date parts to integers
    var parts = dateTimeValue.split('T');
    date = parts[0].split('-');
    time = parts[1].split(':');

    var day = parseInt(date[2], 10);
    var month = parseInt(date[1], 10);
    var year = parseInt(date[0], 10);

    var hour = parseInt(time[0], 10)
    var minute = parseInt(time[1], 10)

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month === 0 || month > 12) {
        setLabel(labelId);
        setMessage('Invalid date format!');
        dateTime.focus();
        return false;
    }

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 === 0 || (year % 100 !== 0 && year % 4 === 0))
        monthLength[1] = 29;

    // Check the range of the day
    if (day <= 0 || day > monthLength[month - 1]) {
        setLabel(labelId);
        setMessage('Invalid date format!');
        dateTime.focus();
        return false;
    }

    // Invalid time format
    if(hour < 0 || hour > 23 || minute < 0 || minute > 59) {
        setLabel(labelId);
        setMessage('Invalid time format!');
        dateTime.focus();
        return false;
    }

    return true;
}
