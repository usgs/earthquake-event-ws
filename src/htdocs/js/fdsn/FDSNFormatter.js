'use strict';

var formatDateTimeFromDate = function (date) {
  var day,
      hours,
      minutes,
      month,
      seconds,
      year;

  day = null;
  hours = null;
  minutes = null;
  month = null;
  seconds = null;
  year = null;

  year = date.getUTCFullYear();
  month = date.getUTCMonth() + 1;
  day = date.getUTCDate();
  hours = date.getUTCHours();
  minutes = date.getUTCMinutes();
  seconds = date.getUTCSeconds();

  if (month < 10) { month = '0' + month; }
  if (day < 10) { day = '0' + day; }
  if (hours < 10) { hours = '0' + hours; }
  if (minutes < 10) { minutes = '0' + minutes; }
  if (seconds < 10) { seconds = '0' + seconds; }

  return year + '-' + month + '-' + day + ' ' + hours + ':' +
      minutes + ':' + seconds;
};

var formatDateTimeFromStamp = function (timeStamp) {
  var date;

  date = new Date(timeStamp);

  return formatDateTimeFromDate(date);
};


var formatDateTimeFromString = function (stamp) {
  var formattedStamp,
      hms,
      parts,
      ymd;

  parts = stamp.split(/( |T)/);
  ymd = parts[0] || '';
  hms = parts[1] || '';
  formattedStamp = null;

  if (parts.length > 2) {
    throw 'Invalid date format';
  }

  ymd = ymd.split('-');
  hms = hms.split(':');

  // Use Date.UTC to perform date math on input values
  formattedStamp = new Date(Date.UTC(ymd[0] || 0, (ymd[1] - 1) || 0,
      ymd[2] || 1, hms[0] || 0, hms[1] || 0, hms[2] || 0));

  return formatDateTimeFromDate(formattedStamp);
};


var formatDateTime = function (obj) {
  try {
    if (typeof obj === 'string') {
      return formatDateTimeFromString(obj);
    } else if (typeof obj === 'number') {
      return formatDateTimeFromStamp(obj);
    } else {
      return formatDateTimeFromDate(obj);
    }
  } catch (e) {
    return obj;
  }
};

var FDSNFormatter = {
  formatDateTime: formatDateTime
};

module.exports = FDSNFormatter;
