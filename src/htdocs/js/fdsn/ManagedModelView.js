/* global document */

/**
* This class manages a set of fields on a model.
*
* Its DOM element displays a message to the user indicating if one or more of
* the fields has a non-default value. If so, a control is provided for the
* user to quickly reset the managed fields.
*
*/

'use strict';

var Model = require('mvc/Model'),
    Util = require('util/Util');

var DEFAULTS = {
  // Text to display when all managed fields are at default values
  clearedText: 'Fieldset Currently Default',

  // Text to display when any managed field is non-default value
  filledText: 'Fieldset Currently Modified',

  // Text to display on the control that will reset to defaults
  controlText: 'Reset to Defaults',

  // Hash of {fieldName: defaultValue} for fields to manage on the model
  fields: {}

  // The following "defaults" are {Object}s and should NOT be uncommented!
  // Defaults are static and would causes all instances to share one object.
  // Constructor will create a new instance if required. The commented field
  // exists here for documentation purposes only.

  // The DOMElement where messages and the control are located
  // el: null,

  // The model on which to manage the fields
  // model: null
};

var ManagedModelView = function (options) {
  var _this,
      _initialize,
      _isClear,
      _onControlClick,
      _onFieldChange;

  //constructor: ManagedModelView,

  _this = {};

  _initialize = function () {
    var fieldName = null;

    options = Util.extend({}, DEFAULTS, options);
    _this._fields = options.fields;
    _this.el = options.el || document.createElement('p');
    _this._model = options.model || new Model();
    _this.el.classList.add('managedmodelview');

    _this.el.innerHTML = [
      '<span class="managedmodelview-message help">',
        options.clearedText,
      '</span>',
      '<span class="managedmodelview-control" style="display:none;">',
        options.controlText,
      '</span>'
    ].join('');

    _this._message = _this.el.querySelector('.managedmodelview-message');
    _this._control = _this.el.querySelector('.managedmodelview-control');

    // Bind to the control
    _this._control.addEventListener('click', (function () {
      return function () {
        _onControlClick();
      };
    })(_this));

    // Bind to the model
    for (fieldName in _this._fields) {
      _this._model.on('change:' + fieldName, _onFieldChange, _this);
    }
  };

  _isClear = function () {
    var fieldName = null;

    for (fieldName in _this._fields) {
      if (_this._model.get(fieldName) !== _this._fields[fieldName]) {
        return false;
      }
    }

    return true;
  };

  _onFieldChange = function () {
    if (_isClear()) {
      // Update message and hide control
      _this._message.innerHTML = options.clearedText;
      _this._control.style.display = 'none';
    } else {
      // Update message and show control
      _this._message.innerHTML = options.filledText;
      _this._control.style.display = '';
    }
  };

  _onControlClick = function () {
    // Set model back to defaults
    _this._model.set(_this._fields);
  };

  _initialize();
  return _this;
};

module.exports = ManagedModelView;
