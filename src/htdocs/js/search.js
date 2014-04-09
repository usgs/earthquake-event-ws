/* global require, FDSN_HOST, FDSN_PATH */
require.config({
	baseUrl: 'js',
	//urlArgs: 'stamp='+(new Date()).getTime(), /* Remove for production */
	paths: {
		mvc: '/hazdev-webutils/src/mvc',
		util: '/hazdev-webutils/src/util'
	}
});


require([
	'fdsn/FDSNSearchForm',
	'fdsn/FDSNModel'
], function (
	FDSNSearchForm,
	FDSNModel
) {
	'use strict';

	new FDSNSearchForm({
		el: document.querySelector('#fdsn-search-form'),
		fieldDataUrl: FDSN_HOST + FDSN_PATH + '/application.json',
		fdsnHost: FDSN_HOST,
		fdsnPath: FDSN_PATH,
		model: new FDSNModel()
	});

});
