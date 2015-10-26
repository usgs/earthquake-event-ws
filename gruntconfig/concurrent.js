'use strict';

var concurrent = {
  build: [
    'browserify:build',
    'postcss:build',
    'copy:build'
  ],

  dist: [
    'htmlmin:dist',
    'uglify',
    'copy:dist',
    'postcss:dist'
  ],

  test: [
    'browserify:test',
    'browserify:bundle',
    'copy:test'
  ]
};


module.exports = concurrent;
