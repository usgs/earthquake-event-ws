/* global require */
require.config({
	baseUrl: 'js',
	urlArgs: "stamp="+(new Date()).getTime() /* Remove for production */
});


require([
	'fdsn/FDSNSearchForm',
	'fdsn/FDSNModel'
], function (
	FDSNSearchForm,
	FDSNModel
) {
	'use strict';

	var _application = new FDSNSearchForm({
		el: document.querySelector('#fdsn-search-form'),
		fieldDataUrl: FDSN_HOST + FDSN_PATH + '/application.json',
		fdsnHost: FDSN_HOST,
		fdsnPath: FDSN_PATH,
		model: new FDSNModel()
	});

});
