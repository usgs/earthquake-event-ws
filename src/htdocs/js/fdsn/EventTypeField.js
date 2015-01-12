/* global define */
define([
	'fdsn/SelectField',
	'util/Util'
], function (
	SelectField,
	Util
) {
	'use strict';

	var EQ_EVENT_TYPES = {
		'earthquake': true,
		'induced or triggered event': true
	};

	var EventTypeField = function (options) {
		// Call parent constructor
		SelectField.call(this, options);
	};

	EventTypeField.prototype = Util.extend({}, SelectField.prototype, {
		_initialize: function () {
			this._createContainers();
			this._createFields();
		},

		_createContainers: function () {
			var eqcontainer, noneqcontainer;

			this._el.innerHTML = [
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

			this._eqcontainer = eqcontainer =
					this._el.querySelector('.eqeventtype-list');
			this._noneqcontainer = noneqcontainer =
					this._el.querySelector('.noneqeventtype-list');

			Util.addEvent(this._el.querySelector('.eqeventtype-control'), 'change',
			function () {
				var inputs = eqcontainer.querySelectorAll('input'),
				    i = 0, len = inputs.length,
				    checked = this.checked;

				for (; i < len; i++) {
					inputs[i].checked = checked;
				}
			});

			Util.addEvent(this._el.querySelector('.noneqeventtype-control'), 'change',
			function () {
				var inputs = noneqcontainer.querySelectorAll('input'),
				    i = 0, len = inputs.length,
				    checked = this.checked;

				for (; i < len; i++) {
					inputs[i].checked = checked;
				}
			});
		},

		_createFields: function () {
			var i = 0,
			    len = this._fields.length,
			    field = null,
			    eqmarkup = [],
			    noneqmarkup = [];

			for (; i < len; i++) {
				field = this._fields[i];

				if (this._isEqEventType(field)) {
					eqmarkup.push(this._createField(field));
				} else {
					noneqmarkup.push(this._createField(field));
				}
			}

			this._eqcontainer.innerHTML = eqmarkup.join('');
			this._noneqcontainer.innerHTML = noneqmarkup.join('');
		},

		_isEqEventType: function (type) {
			var key = this._getKey(type);

			return (key in EQ_EVENT_TYPES);
		},

		_getKey: function (type) {
			return type.replace(/ /g, '_');
		}
	});

	return EventTypeField;
});
