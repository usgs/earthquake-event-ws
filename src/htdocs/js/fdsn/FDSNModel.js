'use strict';

var Model = require('mvc/Model'),
    Util = require('util/Util');

var NUMERIC_TYPES = {
  maxlatitude: true,
  minlatitude: true,
  maxlongitude: true,
  minlongitude: true,

  latitude: true,
  longitude: true,
  maxradiuskm: true,

  minmagnitude: true,
  maxmagnitude: true,

  mindepth: true,
  maxdepth: true,

  mingap: true,
  maxgap: true,

  minsig: true,
  maxsig: true,

  minmmi: true,
  maxmmi: true,

  mincdi: true,
  maxcdi: true,
  minfelt: true,

  limit: true,
  offset: true
};

// Enumeration of all FDSN fields
var DEFAULT_DATA = {
  starttime: '',
  enddtime: '',
  basictime: null,

  maxlatitude: null,
  minlatitude: null,
  maxlongitude: null,
  minlongitude: null,

  latitude: null,
  longitude: null,
  maxradiuskm: null,

  basicmagnitude: null,
  minmagnitude: '',
  maxmagnitdue: null,

  mindepth: null,
  maxdepth: null,

  mingap: null,
  maxgap: null,

  reviewstatus: null,
  //magnitudetype: null,
  eventtype: null,

  minsig: null,
  maxsig: null,

  alertlevel: null,

  minmmi: null,
  maxmmi: null,

  mincdi: null,
  maxcdi: null,
  minfelt: null,

  catalog: null,
  contributor: null,

  format: 'maplist',

  kmlcolorby: null,
  kmlanimated: null,

  callback: null,
  jsonerror: null,

  includeallorigins: null,
  includeallmagnitudes: null
  // TODO :: Implement includearrivals
  //includearrivals: null
};

var _formatDateTime = function (stamp) {
  var parts = stamp.split(/( |T)/),
      ymd = parts[0] || '',
      hms = parts[1] || '',
      formattedStamp = null,
      year = null, month = null, day = null,
      hours = null, minutes = null, seconds = null;

  try {

    if (parts.length > 2) {
      throw 'Invalid date format';
    }

    ymd = ymd.split('-');
    hms = hms.split(':');

    // Use Date.UTC to perform date math on input values
    formattedStamp = new Date(Date.UTC(ymd[0] || 0, (ymd[1] - 1) || 0,
        ymd[2] || 1, hms[0] || 0, hms[1] || 0, hms[2] || 0));

    year = formattedStamp.getUTCFullYear();
    month = formattedStamp.getUTCMonth() + 1;
    day = formattedStamp.getUTCDate();
    hours = formattedStamp.getUTCHours();
    minutes = formattedStamp.getUTCMinutes();
    seconds = formattedStamp.getUTCSeconds();

    if (month < 10) { month = '0' + month; }
    if (day < 10) { day = '0' + day; }
    if (hours < 10) { hours = '0' + hours; }
    if (minutes < 10) { minutes = '0' + minutes; }
    if (seconds < 10) { seconds = '0' + seconds; }

    return year + '-' + month + '-' + day + ' ' + hours + ':' +
        minutes + ':' + seconds;
  } catch (e) {
    // Couldn't parse, use original, let server try
    return stamp;
  }
};

var FDSNModel = function (data) {
  var _parentSet,
      _this;

  _this = Model(Util.extend({}, DEFAULT_DATA, data));
  data = null;

  // Keep a reference to Model.set()
  _parentSet = _this.set;

  _this.set = function (params, options) {
    var key = null;

    // Checks for basic time 7 Days, 24 hours, or Custom time.
    if (params.hasOwnProperty('basictime') && params.basictime !== '') {
      params.starttime = _formatDateTime(params.basictime);
    } else {
      // Format date/time stamps (if possible)
      if (params.hasOwnProperty('starttime') && params.starttime !== '') {
        params.starttime = _formatDateTime(params.starttime);
      }
    }
    if (params.hasOwnProperty('endtime') && params.endtime !== '') {
      params.endtime = _formatDateTime(params.endtime);
    }

    if (params.hasOwnProperty('basicmagnitude') && params.basictime !== '') {
      if (params.basicmagnitude === 'greaterfour') {
        params.minmagnitude = '4';
        params.maxmagnitude = '';
      }
      if (params.basicmagnitude === 'lessfour') {
        params.minmagnitude = '';
        params.maxmagnitude = '4';
      }
    }


    // Convert numeric types to numbers (for comparison later)
    for (key in params) {
      if (NUMERIC_TYPES.hasOwnProperty(key) && params[key] !== null &&
          params[key] !== '') {
        params[key] = Number(params[key]);
      }
    }

    _parentSet(params, options);
  };

  /**
   * A lot like generic "set" method, however the given params are extended by
   * the defaults thus clearing/resetting any parameter that is not explicitely
   * specified in {params}.
   *
   * @param params {Object}
   *      A hash of params to set.
   */
  _this.setAll = function (params) {
    _this.set(Util.extend({}, DEFAULT_DATA, params));
  };

  /**
   * Returns an object containing non-empty, non-null key-value pairs from this
   * model's attributes.
   *
   * @return {Object}
   */
  _this.getNonEmpty = function (model) {
    var nonEmpty = {},
        key = null;

    model = model || _this.get();

    for (key in model) {
      if (model.hasOwnProperty(key) && typeof model[key] !== 'undefined' &&
          model[key] !== null && model[key] !== '') {

        nonEmpty[key] = model[key];
      }
    }

    return nonEmpty;
  };

  return _this;
};

module.exports = FDSNModel;
