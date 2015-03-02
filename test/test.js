/* global mocha */

(function () {
  'use strict';

  mocha.ui('bdd');
  mocha.reporter('html');

  // TODO, create tests

  if (window.mochaPhantomJS) {
    window.mochaPhantomJS.run();
  } else {
    mocha.run();
  }

})(this);