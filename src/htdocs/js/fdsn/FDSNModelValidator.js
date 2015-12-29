'use strict';

var FDSNModel = require('fdsn/FDSNModel'),
    Util = require('util/Util');

var FIELD_LABELS = {
  maxlatitude: 'Rectangle Latitude',
  minlatitude: 'Rectangle Latitude',
  maxlongitude: 'Rectangle Longitude',
  minlongitude: 'Rectangle Longitude',

  maxradiuskm: 'Circle',

  mindepth: 'Depth',
  maxdepth: 'Depth',

  mingap: 'Azimuthal Gap',
  maxgap: 'Azimuthal Gap',

  minsig: 'Significance',
  maxsig: 'Significance',

  minmmi: 'ShakeMap MMI',
  maxmmi: 'ShakeMap MMI',

  mincdi: 'Did You Feel It CDI',
  maxcdi: 'Did You Feel It CDI',
  minfelt: 'Number of DYFI? Responses'
};

var _setError = function (errors, errorName, errorMessage, errorField) {
  var errorList;

  // Don't add null-keyed or null-valued errors
  if (errorName === null || errorMessage === null) {
    return errors;
  }

  errorList = errors.hasOwnProperty(errorName) ? errors[errorName] : {};

  if (!errorList.hasOwnProperty(errorMessage)) {
    errorList[errorMessage] = [];
  }

  errorList[errorMessage].push(errorField);

  errors[errorName] = errorList;

  return errors;
};

var _validate = function (params) {
  var errors = {},
      key = null,
      otherKey = null,
      value = null;

  // --- Check min/max field pairings -- //

  for (key in params) {
    value = params[key];

    if (key.indexOf('min') === 0) {
      otherKey = key.replace('min', 'max');

      if (params.hasOwnProperty(otherKey) &&
          params[key] > params[otherKey]) {
        _setError(errors, FIELD_LABELS[key] || key.replace('min', ''),
            'Minimum must be smaller than maximum.', key);
      }

    } else if (key.indexOf('max') === 0) {
      otherKey = key.replace('max', 'min');

      if (params.hasOwnProperty(otherKey) &&
          params[otherKey] > params[key]) {
        _setError(errors, FIELD_LABELS[key] || key.replace('max', ''),
            'Minimum must be smaller than maximum.', key);
      }

    }

  }

  // -- Check for field combination completeness (as appropriate) -- //
  if (params.hasOwnProperty('latitude') ||
      params.hasOwnProperty('longitude') ||
      params.hasOwnProperty('maxradiuskm')) {

    // Trying to do a circle search. Make sure minimum set of fields are set.
    if (!params.hasOwnProperty('latitude')) {
      _setError(errors, 'Circle',
          'Center latitude/longitude must be specified together.',
          'latitude');
    }
    if (!params.hasOwnProperty('longitude')) {
      _setError(errors, 'Circle',
          'Center latitude/longitude must be specified together.',
          'longitude');
    }

    if (!params.hasOwnProperty('maxradiuskm')) {
      _setError(errors, 'Circle',
          'Circle searches require an outer radius.', 'maxradiuskm');
    }
  }

  // TODO :: Other tests?

  return errors;
};

var DEFAULTS = {
  // TODO :: Anything?
};

var FDSNModelValidator = function (options) {
  var _errors,
      _model,
      _this,

      _getErrorFields,
      _getErrors,
      _initialize,
      _onModelChange;

  _this = {};

  _initialize = function () {
    options = Util.extend({}, DEFAULTS, options);

    _model = options.model || FDSNModel();
    _errors = _validate(_model.getNonEmpty(), {});
    _model.on('change', _onModelChange);
  };

  /**
   * @return {Object}
   *      An object hash of {fieldName: errorMessage} for current errors on
   *      _this._model.
   */
  _getErrors = function () {
    return _errors;
  };

  /**
   * @return {Array}
   *      An array containing the name of the fields that are currently in an
   *      error state.
   */
  _getErrorFields = function () {
    var fields = [],
        key = null;

    for (key in _errors) {
      fields.push(key);
    }

    return fields;
  };

  /**
   * @return {Boolean}
   *      True if the combination of field values in _this._model currently
   *      valid, false otherwise.
   */
  _this.isValid = function () {
    var key = null;

    for (key in _errors) {
      // If any keys in _this._errors, then not valid
      return false;
    }

    return true;
  };

  /**
   * Event handler when _this._model changes. Auto-validates the model new model
   *
   */
  _onModelChange = function () {
    _errors = _validate(_model.getNonEmpty());
  };

  _initialize();
  return _this;
};

module.exports = FDSNModelValidator;
