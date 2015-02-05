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
	this._options = Util.extend({}, DEFAULTS, options);

	this._section = options.section || document.createElement('section');
	this._control = options.control || document.createElement('control');

	this._initialize();
};

ToggleSection.prototype = {
	constructor: ToggleSection,

	_initialize: function () {

		// Add basic classes
		if (!Util.hasClass(this._section, TOGGLE_CLASS)) {
			Util.addClass(this._section, TOGGLE_CLASS);
		}
		if (!Util.hasClass(this._control, TOGGLE_CONTROL_CLASS)) {
			Util.addClass(this._control, TOGGLE_CONTROL_CLASS);
		}

		if (this._options.defaultVisible === true &&
				!Util.hasClass(this._section, TOGGLE_VISIBLE_CLASS)) {
			// Does want section visible by default
			Util.addClass(this._section, TOGGLE_VISIBLE_CLASS);
		} else if (this._options.defaultVisible === false &&
				Util.hasClass(this._section, TOGGLE_VISIBLE_CLASS)) {
			// Does not want section visible by default
			Util.removeClass(this._section, TOGGLE_VISIBLE_CLASS);
		}

		Util.addEvent(this._control, 'click', (function (view) {
			return function (evt) {
				view._onControlClick(evt, this);
			};
		})(this));
	},

	_onControlClick: function () {
		if (Util.hasClass(this._section, TOGGLE_VISIBLE_CLASS)) {
			Util.removeClass(this._section, TOGGLE_VISIBLE_CLASS);
		} else {
			Util.addClass(this._section, TOGGLE_VISIBLE_CLASS);
		}
	}

};

module.exports = ToggleSection;

