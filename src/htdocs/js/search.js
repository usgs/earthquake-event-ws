/* global require, FDSN_HOST, FDSN_PATH */

'use strict';

var FDSNSearchForm = require('./fdsn/FDSNSearchForm'),
		FDSNModel = require('./fdsn/FDSNModel');

new FDSNSearchForm({
	el: document.querySelector('#fdsn-search-form'),
	fieldDataUrl: FDSN_HOST + FDSN_PATH + '/application.json',
	fdsnHost: FDSN_HOST,
	fdsnPath: FDSN_PATH,
	model: new FDSNModel()
});

