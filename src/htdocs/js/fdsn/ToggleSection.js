/* global document */

'use strict';

var Util = require('util/Util');

var TOGGLE_CLASS = 'toggle',
    TOGGLE_VISIBLE_CLASS = 'toggle-visible',
    TOGGLE_CONTROL_CLASS = 'toggle-control';

var DEFAULTS = {
  defaultVisible: false
};

var ToggleSection = function (options) {

  var _this,
      _initialize;

  _this = {};

  //constructor: ToggleSection,

  _initialize = function () {

    options = Util.extend({}, DEFAULTS, options);

    _this._section = options.section || document.createElement('section');
    _this._control = options.control || document.createElement('control');
  
    // Add basic classes
    if (_this._section.classList.contains(TOGGLE_CLASS)) {
      _this._section.classList.add(TOGGLE_CLASS);
    }
    if (_this._control.classList.contains(TOGGLE_CONTROL_CLASS)) {
      _this._control.classList.add(TOGGLE_CONTROL_CLASS);
    }

    if (options.defaultVisible === true &&
        !_this._section.classList.contains(TOGGLE_VISIBLE_CLASS)) {
      // Does want section visible by default
      _this._section.classList.add(TOGGLE_VISIBLE_CLASS);
    } else if (options.defaultVisible === false &&
        _this._section.classList.contains(TOGGLE_VISIBLE_CLASS)) {
      // Does not want section visible by default
      _this._section.classList.remove(TOGGLE_VISIBLE_CLASS);
    }

    _this._control.addEventListener('click', (function (view) {
      return function (evt) {
        view._onControlClick(evt, _this);
      };
    })(_this));
  };

  _this._onControlClick = function () {
    if (_this._section.classList.contains(TOGGLE_VISIBLE_CLASS)) {
      _this._section.classList.remove(TOGGLE_VISIBLE_CLASS);
    } else {
      _this._section.classList.add(TOGGLE_VISIBLE_CLASS);
    }
  };

  _initialize();
  return _this;
};

module.exports = ToggleSection;

