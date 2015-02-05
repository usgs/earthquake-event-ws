/* global mocha */

(function () {
	'use strict';

	mocha.setup('bdd');
	mocha.reporter('html');


	if (window.mochaPhantomJS) {
		window.mochaPhantomJS.run();
	} else {
		mocha.run();
	}

})(this);