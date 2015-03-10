'use strict';

var Util = require('util/Util');

var TOGGLE_CLASS = 'toggle',
    TOGGLE_VISIBLE_CLASS = 'toggle-visible',
    TOGGLE_CONTROL_CLASS = 'toggle-control';

var DEFAULTS = {
  defaultVisible: false
};

var ToggleSection = function (options) {

  var _control,
      _section,
      _this,

      _initialize,
      _onControlClick;

  _this = {};

  //constructor: ToggleSection,

  _initialize = function () {

    options = Util.extend({}, DEFAULTS, options);

    _section = options.section;
    _control = options.control;

    // Add basic classes
    if (!_section.classList.contains(TOGGLE_CLASS)) {
      _section.classList.add(TOGGLE_CLASS);
    }
    if (!_control.classList.contains(TOGGLE_CONTROL_CLASS)) {
      _control.classList.add(TOGGLE_CONTROL_CLASS);
    }

    if (options.defaultVisible === true &&
        !_section.classList.contains(TOGGLE_VISIBLE_CLASS)) {
      // Does want section visible by default
      _section.classList.add(TOGGLE_VISIBLE_CLASS);
    } else if (options.defaultVisible === false &&
        _section.classList.contains(TOGGLE_VISIBLE_CLASS)) {
      // Does not want section visible by default
      _section.classList.remove(TOGGLE_VISIBLE_CLASS);
    }

    _control.addEventListener('click', _onControlClick);
  };

  _onControlClick = function () {
    if (_section.classList.contains(TOGGLE_VISIBLE_CLASS)) {
      _section.classList.remove(TOGGLE_VISIBLE_CLASS);
    } else {
      _section.classList.add(TOGGLE_VISIBLE_CLASS);
    }
  };

  _initialize();
  return _this;
};

module.exports = ToggleSection;

