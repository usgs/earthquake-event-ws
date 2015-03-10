'use strict';

var SelectField = require('fdsn/SelectField'),
    Util = require('util/Util');

var EQ_EVENT_TYPES = {
  'earthquake': true,
  'induced or triggered event': true
};

var EventTypeField = function (options) {
  var _eqcontainer,
      _eqcontrol,
      _noneqcontainer,
      _noneqcontrol,
      _this,

      _createContainers,
      _createFields,
      _getKey,
      _initialize,
      _isEqEventType,
      _toggleAll;

  _this = SelectField(options);

  _initialize = function () {
    options = Util.extend({}, SelectField.prototype, options);

    _createContainers();
    _createFields();
  };

  _createContainers = function () {

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

    _eqcontainer = _this.el.querySelector('.eqeventtype-list');
    _noneqcontainer = _this.el.querySelector('.noneqeventtype-list');
    _eqcontrol = _this.el.querySelector('.eqeventtype-control');
    _noneqcontrol = _this.el.querySelector('.noneqeventtype-control');

    _eqcontrol.addEventListener('change', _toggleAll);
    _noneqcontrol.addEventListener('change', _toggleAll);
  };

  _toggleAll = function () {
    var inputs = this.parentElement.nextSibling.querySelectorAll('input'),
        i = 0, len = inputs.length,
        checked = this.checked;

    for (; i < len; i++) {
      inputs[i].checked = checked;
    }
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

    _eqcontainer.innerHTML = eqmarkup.join('');
    _noneqcontainer.innerHTML = noneqmarkup.join('');
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
