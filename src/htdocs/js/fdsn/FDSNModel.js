'use strict';

var Model = require('mvc/Model'),
		Util = require('util/Util');

// Default configuration options
var DEFAULTS = {
};

var NUMERIC_TYPES = {
	maxlatitude: true,
	minlatitude: true,
	maxlongitude: true,
	minlongitude: true,

	latitude: true,
	longitude: true,
	minradiuskm: true,
	maxradiuskm: true,

	minmagnitude: true,
	maxmagnitude: true,

	mindepth: true,
	maxdepth: true,

	mingap: true,
	maxgap: true,

	minsig: true,
	maxsig: true,

	minmmi: true,
	maxmmi: true,

	mincdi: true,
	maxcdi: true,
	minfelt: true,

	limit: true,
	offset: true
};

// Enumeration of all FDSN fields
var DEFAULT_DATA = {
	starttime: '',
	enddtime: '',

	maxlatitude: null,
	minlatitude: null,
	maxlongitude: null,
	minlongitude: null,

	latitude: null,
	longitude: null,
	minradiuskm: null,
	maxradiuskm: null,

	minmagnitude: '',
	maxmagnitdue: null,

	mindepth: null,
	maxdepth: null,

	mingap: null,
	maxgap: null,

	reviewstatus: null,
	//magnitudetype: null,
	eventtype: null,

	minsig: null,
	maxsig: null,

	alertlevel: null,

	minmmi: null,
	maxmmi: null,

	mincdi: null,
	maxcdi: null,
	minfelt: null,

	catalog: null,
	contributor: null,

	format: null,

	kmlcolorby: null,
	kmlanimated: null,

	callback: null,
	jsonerror: null,

	includeallorigins: null,
	includeallmagnitudes: null
	// TODO :: Implement includearrivals
	//includearrivals: null
};

var _formatDateTime = function (stamp) {
	var parts = stamp.split(/( |T)/),
	    ymd = parts[0] || '',
	    hms = parts[1] || '',
	    formattedStamp = null,
	    year = null, month = null, day = null,
	    hours = null, minutes = null, seconds = null;

	try {

		if (parts.length > 2) {
			throw 'Invalid date format';
		}

		ymd = ymd.split('-');
		hms = hms.split(':');

		// Use Date.UTC to perform date math on input values
		formattedStamp = new Date(Date.UTC(ymd[0] || 0, (ymd[1] - 1) || 0,
				ymd[2] || 1, hms[0] || 0, hms[1] || 0, hms[2] || 0));

		year = formattedStamp.getUTCFullYear();
		month = formattedStamp.getUTCMonth() + 1;
		day = formattedStamp.getUTCDate();
		hours = formattedStamp.getUTCHours();
		minutes = formattedStamp.getUTCMinutes();
		seconds = formattedStamp.getUTCSeconds();

		if (month < 10) { month = '0' + month; }
		if (day < 10) { day = '0' + day; }
		if (hours < 10) { hours = '0' + hours; }
		if (minutes < 10) { minutes = '0' + minutes; }
		if (seconds < 10) { seconds = '0' + seconds; }

		return year + '-' + month + '-' + day + ' ' + hours + ':' +
				minutes + ':' + seconds;
	} catch (e) {
		// Couldn't parse, use original, let server try
		return stamp;
	}
};

var FDSNModel = function (data, options) {
	// Extend default options
	this._options = Util.extend({}, DEFAULTS, options);

	// Call parent constructor
	Model.call(this, Util.extend({}, DEFAULT_DATA, data));
};

FDSNModel.prototype = Object.create(Model.prototype);

FDSNModel.prototype.set = function (params, options) {
	var key = null;

	// Format date/time stamps (if possible)
	if (params.hasOwnProperty('starttime') && params.starttime !== '') {
		params.starttime = _formatDateTime(params.starttime);
	}
	if (params.hasOwnProperty('endtime') && params.endtime !== '') {
		params.endtime = _formatDateTime(params.endtime);
	}

	// Convert numeric types to numbers (for comparison later)
	for (key in params) {
		if (NUMERIC_TYPES.hasOwnProperty(key) && params[key] !== null &&
				params[key] !== '') {
			params[key] = Number(params[key]);
		}
	}

	Model.prototype.set.call(this, params, options);
};

/**
 * A lot like generic "set" method, however the given params are extended by
 * the defaults thus clearing/resetting any parameter that is not explicitely
 * specified in {params}.
 *
 * @param params {Object}
 *      A hash of params to set.
 */
FDSNModel.prototype.setAll = function (params) {
	this.set(Util.extend({}, DEFAULTS, params));
};

/**
 * Returns an object containing non-empty, non-null key-value pairs from this
 * model's attributes.
 *
 * @return {Object}
 */
FDSNModel.prototype.getNonEmpty = function (model) {
	var nonEmpty = {},
	    key = null;

	model = model || this._model;

	for (key in model) {
		if (model.hasOwnProperty(key) && typeof model[key] !== 'undefined' &&
				model[key] !== null && model[key] !== '') {

			nonEmpty[key] = model[key];
		}
	}

	return nonEmpty;
};

module.exports = FDSNModel;
