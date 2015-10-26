'use strict';

var config = require('./config');


var watch = {
  scripts: {
    files: [
      config.src + '/htdocs/js/**/*.js'
    ],
    tasks: [
      'jshint:scripts',
      'browserify:build',
      'browserify:bundle',
      'browserify:test',
      'browserify:bundle',
      'copy:test',
      'mocha_phantomjs'
    ]
  },
  scss: {
    files: [
      config.src + '/htdocs/css/**/*.scss'
    ],
    tasks: ['postcss:build']
  },
  test_js: {
    files: [
      config.test + '/**/*.js'
    ],
    tasks: ['jshint:tests', 'browserify:test', 'copy:test']
  },
  test_html: {
    files: [
      config.test + '/*.html'
    ],
    tasks: ['copy:test']
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
      livereload: config.liveReloadPort
    },
    files: [
      config.build + '/' + config.src + '/htdocs/**/*'
    ]
  },
  gruntfile: {
    files: [
      'Gruntfile.js',
      'gruntconfig/**/*'
    ],
    tasks: ['jshint:gruntfile']
  }
};


module.exports = watch;
