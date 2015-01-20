/* global define, document */

/**
 * This class manages a set of fields on a model.
 *
 * Its DOM element displays a message to the user indicating if one or more of
 * the fields has a non-default value. If so, a control is provided for the
 * user to quickly reset the managed fields.
 *
 */
define([
	'mvc/Model',
	'util/Util'
], function (
	Model,
	Util
) {
	'use strict';

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
		this._options = Util.extend({}, DEFAULTS, options);
		this._fields = this._options.fields;
		this._el = this._options.el || document.createElement('p');
		this._model = this._options.model || new Model();
		Util.addClass(this._el, 'managedmodelview');

		this._initialize();
	};

	ManagedModelView.prototype = {
		constructor: ManagedModelView,

		_initialize: function () {
			var fieldName = null;

			this._el.innerHTML = [
				'<span class="managedmodelview-message help">',
					this._options.clearedText,
				'</span>',
				'<span class="managedmodelview-control" style="display:none;">',
					this._options.controlText,
				'</span>'
			].join('');

			this._message = this._el.querySelector('.managedmodelview-message');
			this._control = this._el.querySelector('.managedmodelview-control');

			// Bind to the control
			Util.addEvent(this._control, 'click', (function (view) {
				return function () {
					view._onControlClick();
				};
			})(this));

			// Bind to the model
			for (fieldName in this._fields) {
				this._model.on('change:' + fieldName, this._onFieldChange, this);
			}

		},

		_isClear: function () {
			var fieldName = null;

			for (fieldName in this._fields) {
				if (this._model.get(fieldName) !== this._fields[fieldName]) {
					return false;
				}
			}

			return true;
		},

		_onFieldChange: function () {
			if (this._isClear()) {
				// Update message and hide control
				this._message.innerHTML = this._options.clearedText;
				this._control.style.display = 'none';
			} else {
				// Update message and show control
				this._message.innerHTML = this._options.filledText;
				this._control.style.display = '';
			}
		},

		_onControlClick: function () {
			// Set model back to defaults
			this._model.set(this._fields);
		}

	};

	return ManagedModelView;
});
