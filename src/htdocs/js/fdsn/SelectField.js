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

  var _allowAny,
      _allowAnyText,
      _allowAnyValue,
      _endWrapper,
      _id,
      _startWrapper,
      _this,
      _type,

      _createFields,
      _formatDisplay,
      _formatValue,
      _initialize;

  _this = {};

  _initialize = function () {
    options = Util.extend({}, DEFAULTS, options);

    _this.el = options.el || document.createElement('ul');
    _this._fields = options.fields;

    _type = options.type;
    _id = options.id || 'select-field-' + (DEFAULT_FIELD_ID++);
    _formatDisplay = options.formatDisplay;
    _formatValue = options.formatValue;
    _allowAny = options.allowAny;
    _allowAnyText = options.allowAnyText;
    _allowAnyValue = options.allowAnyValue;

    if (options.wrapper !== '') {
      _startWrapper = '<' + options.wrapper + '>';
      _endWrapper = '</' + options.wrapper + '>';
    } else {
      _startWrapper = '';
      _endWrapper = '';
    }

    _createFields();
  };

  _createFields = function () {
    var i = 0,
        len = _this._fields.length,
        markup = [];

    if (_type === 'radio' && _allowAny === true) {
      markup.push(_this._createField(_allowAnyText,
          _allowAnyValue, true));
    }
    for (; i < len; i++) {
      markup.push(_this._createField(_this._fields[i]));
    }

    _this.el.innerHTML = markup.join('');
  };

  _this._createField = function (name, value, checked) {
    var idValue,
        textStr,
        valueStr;

    idValue = null;
    textStr = null;
    valueStr = null;

    valueStr = _formatValue(
        (typeof value !== 'undefined') ? value : name);
    textStr = _formatDisplay(name);

    if (_this._getFieldId(valueStr) === '') {
      idValue = textStr;
    } else {
      idValue = valueStr;
    }

    idValue = idValue.replace(' ', '-');

    return [
      _startWrapper,
      '<input type="', _type, '" name="', _id, '" id="',
          _id,
          '-',
          idValue,
          '" value="', valueStr, '"', ((checked)?' checked':''),'/>',
      '<label class="label-checkbox" for="',
          _id,
          '-',
          idValue,
          '">',
        textStr,
      '</label>',
      _endWrapper
    ].join('');
  };

  _this._getFieldId = function (value) {
    //replace spaces with double underscore in case one value uses underscore
    //and another uses a space, that are otherwise the same
    return _id + '-' + value.replace('/ /g', '__');
  };

  _initialize();
  return _this;
};

module.exports = SelectField;
