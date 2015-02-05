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
	options = Util.extend({}, DEFAULTS, options);

	this._el = options.el || document.createElement('ul');
	this._type = options.type;
	this._fields = options.fields;
	this._id = options.id || 'select-field-' + (DEFAULT_FIELD_ID++);
	this._formatDisplay = options.formatDisplay;
	this._formatValue = options.formatValue;
	this._allowAny = options.allowAny;
	this._allowAnyText = options.allowAnyText;
	this._allowAnyValue = options.allowAnyValue;

	if (options.wrapper !== '') {
		this._startWrapper = '<' + options.wrapper + '>';
		this._endWrapper = '</' + options.wrapper + '>';
	} else {
		this._startWrapper = '';
		this._endWrapper = '';
	}

	this._initialize();
};

SelectField.prototype = {
	_initialize: function () {
		this._createFields();
	},

	_createFields: function () {
		var i = 0,
		    len = this._fields.length,
		    markup = [];

		if (this._type === 'radio' && this._allowAny === true) {
			markup.push(this._createField(this._allowAnyText,
					this._allowAnyValue, true));
		}
		for (; i < len; i++) {
			markup.push(this._createField(this._fields[i]));
		}

		this._el.innerHTML = markup.join('');
	},

	_createField: function (name, value, checked) {
		var valueStr = null,
		    textStr = null;

		valueStr = this._formatValue(
				(typeof value !== 'undefined') ? value : name);
		textStr = this._formatDisplay(name);

		return [
			this._startWrapper,
			'<label class="label-checkbox">',
				'<input type="', this._type, '" name="', this._id, '" id="',
						this._getFieldId((valueStr === '') ? textStr : valueStr),
						'" value="', valueStr, '"', ((checked)?' checked':''),'/>',
				textStr,
			'</label>',
			this._endWrapper
		].join('');
	},

	_getFieldId: function (value) {
		//replace spaces with double underscore in case one value uses underscore
		//and another uses a space, that are otherwise the same
		return this._id + '-' + value.replace(/ /g, '__');
	}
};

module.exports = SelectField;
