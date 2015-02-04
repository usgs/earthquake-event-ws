'use strict';

var config = require('./config');

var jshint = {
  options: {
    jshintrc: '.jshintrc'
  },
  gruntfile: ['Gruntfile.js', 'gruntconfig/*'],
  scripts: [
    config.src + '/htdocs/js/**/*.js'
  ],
  tests: [
    config.test + '/**/*.js'
  ]
};

module.exports = jshint;