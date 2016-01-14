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
  starttime: null,
  enddtime: null,

  maxlatitude: null,
  minlatitude: null,
  maxlongitude: null,
  minlongitude: null,

  latitude: null,
  longitude: null,
  maxradiuskm: null,

  minmagnitude: null,
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

var FDSNModel = function (data) {
  var _parentSet,
      _this;

  _this = Model(Util.extend({}, DEFAULT_DATA, data));
  data = null;

  // Keep a reference to Model.set()
  _parentSet = _this.set;

  _this.set = function (params, options) {
    var key = null;

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
