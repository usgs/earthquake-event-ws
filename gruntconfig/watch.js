'use strict';

var config = require('./config');

var watch = {
  scripts: {
    files: [
      config.src + '/htdocs/js/**/*.js'
    ],
    tasks: ['jshint:scripts', 'browserify:build']
  },
  scss: {
    files: [
      config.src + '/htdocs/css/**/*.scss'
    ],
    tasks: ['compass:build']
  },
  tests: {
    files: [
      config.test + '/*.html',
      config.test + '/**/*.js'
    ],
    tasks: ['jshint:tests', 'browserify:test', 'copy:test']
  },
  resources: {
    files: [
      config.src + '/conf/**/*',
      config.src + '/lib/**/*',
      config.src + '/htdocs/**/*',
      '!' + config.src + '/htdocs/**/*.{js,scss}'
    ],
    tasks: ['copy:build']
  },
  livereload: {
    options: {
      livereload: true
    },
    files: [
      config.build + '/' + config.src + '/htdocs/**/*'
    ]
  },
  gruntfile: {
    files: [
      'Gruntfile.js'
    ],
    tasks: ['jshint:gruntfile']
  }
};

module.exports = watch;