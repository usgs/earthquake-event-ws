'use strict';

var connect = require('./connect').test;


var mocha_phantomjs = {
  all: {
    options: {
      urls: [
        'http://localhost:' + connect.options.port + '/test.html'
      ]
    }
  }
};


module.exports = mocha_phantomjs;
