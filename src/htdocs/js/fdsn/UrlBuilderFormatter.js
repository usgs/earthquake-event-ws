'use strict';

// key-value mappings of known values

// 2013-08-05 - EMM :: Any value not listed below will be passed through the
//                     _formatString method (see below) and the result will
//                     be used as display text. This makes a "best attempt" at
//                     showing something more human readable. If it doesn't
//                     work, just add the proper value-to-display conversion
//                     mapping to the list of known values below.

var KNOWN_MAGNITUDE_TYPES = {
};
var KNOWN_CATALOGS = {
	'ak': 'AK - Alaska Earthquake Information Center',
	'at': 'AT - Alaska Tsunami Warning Center',
	'ci': 'CI - California Institute of Technology',
	'hv': 'HV - Hawaiian Volcano Observatory',
  'iscgem': 'ISC-GEM',
	'ld': 'LD - Lamont-Doherty Cooperative Seismographic Network',
	'mb': 'MB - Montana Bureau of Mines and Geology',
	'nc': 'NC - Northern California Seismic System',
	'nm': 'NM - St. Louis University',
	'nn': 'NN - University of Nevada',
	'pr': 'PR - Red Sismica de Puerto Rico',
	'pt': 'PT - Pacific Tsunami Warning Center',
	'se': 'SE - Virginia Tech',
	'us': 'US - National Earthquake Information Center, PDE',
	'uu': 'UU - University of Utah',
	'uw': 'UW - University of Washington'
};
var KNOWN_CONTRIBUTORS = {
	'ak': 'AK - Alaska Earthquake Information Center',
	'at': 'AT - Alaska Tsunami Warning Center',
	'ci': 'CI - California Institute of Technology',
	'hv': 'HV - Hawaiian Volcano Observatory',
	'ld': 'LD - Lamont-Doherty Cooperative Seismographic Network',
	'mb': 'MB - Montana Bureau of Mines and Geology',
	'nc': 'NC - Northern California Seismic System',
	'nm': 'NM - St. Louis University',
	'nn': 'NN - University of Nevada',
	'pr': 'PR - Red Sismica de Puerto Rico',
	'pt': 'PT - Pacific Tsunami Warning Center',
	'se': 'SE - Virginia Tech',
	'us': 'US - National Earthquake Information Center, PDE',
	'uu': 'UU - University of Utah',
	'uw': 'UW - University of Washington',
	'atlas': 'ShakeMap Atlas',
	'official': 'Official'
};
var KNOWN_EVENT_TYPES = {
	// Earthquake event types
	'earthquake': 'Earthquake',
	'induced or triggered event': 'Induced or Triggered Event',

	// Non earthquake event types
	'not existing': 'Not Existing',
	'not reported': 'Not Reported',
	'anthropogenic event': 'Anthropogenic Event',
	'collapse': 'Collapse',
	'cavity collapse': 'Cavity Collapse',
	'mine collapse': 'Mine Collapse',
	'building collapse': 'Building Collapse',
	'explosion': 'Explosion',
	'accidental explosion': 'Accidental Explosion',
	'chemical explosion': 'Chemical Explosion',
	'controlled explosion': 'Controlled Explosion',
	'experimental explosion': 'Experimental Explosion',
	'industrial explosion': 'Industrial Explosion',
	'mining explosion': 'Mining Explosion',
	'quarry blast': 'Quarry Blast',
	'road cut': 'Road Cut',
	'blasting levee': 'Blasting Levee',
	'nuclear explosion': 'Nuclear Explosion',
	'rock burst': 'Rock Burst',
	'reservoir loading': 'Reservoir Loading',
	'fluid injection': 'Fluid Injection',
	'fluid extraction': 'Fluid Extraction',
	'crash': 'Crash',
	'plane crash': 'Plane Crash',
	'train crash': 'Train Crash',
	'boat crash': 'Boat Crash',
	'other event': 'Other Event',
	'atmospheric event': 'Atmospheric Event',
	'sonic boom': 'Sonic Boom',
	'sonic blast': 'Sonic Blasy',
	'acoustic noise': 'Acoustic Noise',
	'thunder': 'Thunder',
	'avalanche': 'Avalanch',
	'snow avalanche': 'Snow Avalanche',
	'debris avalanche': 'Debris Avalanche',
	'hydroacoustic event': 'Hydroacoustic Event',
	'ice quake': 'Ice Quake',
	'slide': 'Slide',
	'landslide': 'Landslide',
	'rockslide': 'Rockslide',
	'meteorite': 'Meteorite',
	'volcanic eruption': 'Volcanic Eruption'
};
var KNOWN_PRODUCT_TYPES = {
	'test': 'Test',
	'tectonic-summary': 'Tectonic Summary',
	'shakemap': 'ShakeMap',
	'scitech-link': 'Scitech Link',
	'phase-data': 'Phase Data',
	'p-wave-travel-times': 'P-Wave Travel Times',
	'origin': 'Origin',
	'nearby-cities': 'Nearby Cities',
	'moment-tensor': 'Moment Tensor',
	'losspager': 'LossPAGER',
	'impact-text': 'Impact Text',
	'impact-link': 'Impact Link',
	'image': 'Image',
	'geoserve': 'GeoServe',
	'general-link': 'General Link',
	'focal-mechanism': 'Focal Mechanism',
	'finite-fault': 'Finite Fault',
	'dyfi': 'DYFI?',
	'cap': 'CAP',
	'associate': 'Associate'
};


var _ucword = function (word) {
	return word.charAt(0).toUpperCase() + word.slice(1);
};


var _ucwords = function (words) {
	var ucwords = [], i = 0, len = words.length;

	for (i = 0; i < len; i++) {
		ucwords.push(_ucword(words[i]));
	}

	return ucwords.join(' ');
};


var _formatString = function (value) {
	var result = value;

	// Add spaces
	result = value.replace('_', ' ');

	if (result.indexOf(' ') !== -1) {
		// Multi-word, split and ucwords
		return _ucwords(result.split(' '));
	} else {
		return _ucword(result);
	}
};


var _formatValue = function (value, known) {
	if (known.hasOwnProperty(value)) {
		return known[value];
	} else {
		return _formatString(value);
	}
};


var UrlBuilderFormatter = function () {
	var _this;

	_this = {};

	_this.formatMagnitudeType = function (value) {
		return _formatValue(value, KNOWN_MAGNITUDE_TYPES);
	};

	_this.formatCatalog = function (value) {
		return _formatValue(value, KNOWN_CATALOGS);
	};

	_this.formatContributor = function (value) {
		return _formatValue(value, KNOWN_CONTRIBUTORS);
	};

	_this.formatEventType = function (value) {
		return _formatValue(value, KNOWN_EVENT_TYPES);
	};

	_this.formatProductType = function (value) {
		return _formatValue(value, KNOWN_PRODUCT_TYPES);
	};

	return _this;
};

module.exports = UrlBuilderFormatter;