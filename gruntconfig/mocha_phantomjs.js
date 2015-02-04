'use strict';

var connect = require('./connect').test;

var mocha_phantomjs = {
  all: {
    options: {
      urls: [
        'http://localhost:' + connect.options.port + '/index.html'
      ]
    }
  }
};

module.exports = mocha_phantomjs;