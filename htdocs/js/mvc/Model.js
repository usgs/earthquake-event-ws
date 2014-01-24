/*global define*/

define([
	'util/Util',
	'util/Events'
], function(
	Util,
	Events
) {
	'use strict';

	/**
	 * Constructor.
	 *
	 * @param  data {Object} key/value attributes of this model.
	 */
	var Model = function(data) {
		this._model = Util.extend({}, data);
		// model is source of events
		Events.call(this);

		// track id at top level
		if (data && data.hasOwnProperty('id')) {
			this.id = data.id;
		}
	};

	// model is a source of events
	Model.prototype = Object.create(Events.prototype);

	/**
	 * Update one or more values.
	 *
	 * @param data {Object} the keys and values to update.
	 * @param options {Object} options for this method.
	 * @param options.silent {Boolean} default false.  true to suppress any
	 *                       events that would otherwise be triggered.
	 */
	Model.prototype.set = function(data, options) {
		// detect changes
		var changed = {},
			anyChanged = false,
			c;

		for (c in data) {
			if (!this._model.hasOwnProperty(c) || this._model[c] !== data[c]) {
				changed[c] = data[c];
				anyChanged = true;
			}
		}

		// persist changes
		this._model = Util.extend(this._model, data);

		// if id is changing, update the model id
		if (data && data.hasOwnProperty('id')) {
			this.id = data.id;
		}

		if (options && options.hasOwnProperty('silent') && options.silent) {
			// don't trigger any events
			return;
		}

		// trigger events based on changes
		if (anyChanged ||
				(options && options.hasOwnProperty('force') && options.force)) {
			for (c in changed) {
				// events specific to a property
				this.trigger('change:' + c, changed[c]);
			}
			// generic event for any change
			this.trigger('change', changed);
		}
	};

	/**
	 * Get one or more values.
	 *
	 * @param key {String} the value to get; when key is undefined,
	 *                     returns the object with all values.
	 * @return - if key is specified, the value or null if no value exists.
	 *         - when key is not specified, the underlying object is returned.
	 *         (Any changes to this underlying object will not trigger events!!!)
	 */
	Model.prototype.get = function(key) {
		if (typeof(key) === 'undefined') {
			return this._model;
		}
		if (this._model.hasOwnProperty(key)) {
			return this._model[key];
		}
		return null;
	};


	return Model;
});
