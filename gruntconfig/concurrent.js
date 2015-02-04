'use strict';

var concurrent = {
  scripts: ['jshint:scripts', 'mocha_phantomjs'],
  tests: ['jshint:tests', 'mocha_phantomjs'],
  predist: [
    'jshint:scripts',
    'jshint:tests',
    'compass'
  ],
  dist: [
    'htmlmin:dist',
    'uglify',
    'copy:dist'
  ]
};

module.exports = concurrent;