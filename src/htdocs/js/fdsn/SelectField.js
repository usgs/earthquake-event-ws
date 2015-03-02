/* global document */

'use strict';

var Util = require('util/Util');

var DEFAULT_FIELD_ID = 0;

var DEFAULTS = {
  // NOTE :: Don't specify a default "el" because that will cause all
  // SelectFields to share a single element. Not good.

  type: 'radio', // "checkbox" or "radio"
  fields: [],    // list of field information
  wrapper: 'li', // wrapper to put around each field item

  formatDisplay: function (value) { return value; },
  formatValue: function (value) { return value; },

  allowAny: true, // only used for radio groups
  allowAnyText: 'Any',
  allowAnyValue: ''
};

var SelectField = function (options) {

  var _this,
      _initialize,
      _createFields;

  _this = {};

  _initialize = function () {
    options = Util.extend({}, DEFAULTS, options);

    _this.el = options.el || document.createElement('ul');
    _this._type = options.type;
    _this._fields = options.fields;
    _this._id = options.id || 'select-field-' + (DEFAULT_FIELD_ID++);
    _this._formatDisplay = options.formatDisplay;
    _this._formatValue = options.formatValue;
    _this._allowAny = options.allowAny;
    _this._allowAnyText = options.allowAnyText;
    _this._allowAnyValue = options.allowAnyValue;

    if (options.wrapper !== '') {
      _this._startWrapper = '<' + options.wrapper + '>';
      _this._endWrapper = '</' + options.wrapper + '>';
    } else {
      _this._startWrapper = '';
      _this._endWrapper = '';
    }

    _createFields();
  };

  _createFields = function () {
    var i = 0,
        len = _this._fields.length,
        markup = [];

    if (_this._type === 'radio' && _this._allowAny === true) {
      markup.push(_this._createField(_this._allowAnyText,
          _this._allowAnyValue, true));
    }
    for (; i < len; i++) {
      markup.push(_this._createField(_this._fields[i]));
    }

    _this.el.innerHTML = markup.join('');
  };

  _this._createField = function (name, value, checked) {
    var valueStr = null,
        textStr = null;

    valueStr = _this._formatValue(
        (typeof value !== 'undefined') ? value : name);
    textStr = _this._formatDisplay(name);

    return [
      _this._startWrapper,
      '<label class="label-checkbox">',
        '<input type="', _this._type, '" name="', _this._id, '" id="',
            _this._getFieldId((valueStr === '') ? textStr : valueStr),
            '" value="', valueStr, '"', ((checked)?' checked':''),'/>',
        textStr,
      '</label>',
      _this._endWrapper
    ].join('');
  };

  _this._getFieldId = function (value) {
    //replace spaces with double underscore in case one value uses underscore
    //and another uses a space, that are otherwise the same
    return _this._id + '-' + value.replace(/ /g, '__');
  };

  _initialize();
  return _this;
};

module.exports = SelectField;
