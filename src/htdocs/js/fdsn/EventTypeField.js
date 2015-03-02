'use strict';

var SelectField = require('./SelectField'),
    Util = require('util/Util');

var EQ_EVENT_TYPES = {
  'earthquake': true,
  'induced or triggered event': true
};

var EventTypeField = function (options) {
  var _this,
      _initialize,
      _createContainers,
      _createFields,
      _getKey,
      _isEqEventType;

  _this = SelectField(options);

  _initialize = function () {
    options = Util.extend({}, SelectField.prototype, options);

    _createContainers();
    _createFields();
  };

  _createContainers = function () {
    var eqcontainer, noneqcontainer;

    _this.el.innerHTML = [
      '<li>',
        '<label class="label-checkbox">',
          '<input type="checkbox" class="eqeventtype-control"/>',
          'Earthquakes',
        '</label>',
        '<ul class="eqeventtype-list no-style"></ul>',
      '</li>',
      '<li>',
        '<label class="label-checkbox">',
          '<input type="checkbox" class="noneqeventtype-control"/>',
          'Non-Earthquakes',
        '</label>',
        '<ul class="noneqeventtype-list no-style"></ul>',
      '</li>'
    ].join('');

    _this._eqcontainer = eqcontainer =
        _this.el.querySelector('.eqeventtype-list');
    _this._noneqcontainer = noneqcontainer =
        _this.el.querySelector('.noneqeventtype-list');

    _this.el.querySelector('.eqeventtype-control').addEventListener('change',
    function () {
      var inputs = eqcontainer.querySelectorAll('input'),
          i = 0, len = inputs.length,
          checked = _this.checked;

      for (; i < len; i++) {
        inputs[i].checked = checked;
      }
    });

    _this.el.querySelector('.noneqeventtype-control').addEventListener('change',
    function () {
      var inputs = noneqcontainer.querySelectorAll('input'),
          i = 0, len = inputs.length,
          checked = _this.checked;

      for (; i < len; i++) {
        inputs[i].checked = checked;
      }
    });
  };

  _createFields = function () {
    var i = 0,
        len = _this._fields.length,
        field = null,
        eqmarkup = [],
        noneqmarkup = [];

    for (; i < len; i++) {
      field = _this._fields[i];

      if (_isEqEventType(field)) {
        eqmarkup.push(_this._createField(field));
      } else {
        noneqmarkup.push(_this._createField(field));
      }
    }

    _this._eqcontainer.innerHTML = eqmarkup.join('');
    _this._noneqcontainer.innerHTML = noneqmarkup.join('');
  };

  _isEqEventType = function (type) {
    var key = _getKey(type);

    return (key in EQ_EVENT_TYPES);
  };

  _getKey = function (type) {
    return type.replace(/ /g, '_');
  };

  _initialize();
  return _this;

};

module.exports = EventTypeField;
