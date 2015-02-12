'use strict';

var config = require('./config');

var watch = {
  scripts: {
    files: [
      config.src + '/htdocs/js/**/*.js'
    ],
    tasks: ['concurrent:scripts'],
    options: {
      livereload: true
    }
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
    tasks: ['concurrent:tests']
  },
  livereload: {
    options: {
      livereload: true
    },
    files: [
      config.src + '/htdocs/**/*.html',
      config.src + '/htdocs/css/**/*.css',
      config.src + '/htdocs/img/**/*.{png,jpg,jpeg,gif}',
      config.build + config.src + '/css/**/*.css'
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