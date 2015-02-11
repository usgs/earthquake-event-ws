/* global mocha */

(function () {
	'use strict';

	mocha.setup('bdd');
	mocha.reporter('html');

  // TODO, create test classes

	if (window.mochaPhantomJS) {
		window.mochaPhantomJS.run();
	} else {
		mocha.run();
	}

})(this);