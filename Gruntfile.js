'use strict';

module.exports = function (grunt) {

  var gruntConfig = require('./gruntconfig');

  gruntConfig.tasks.forEach(grunt.loadNpmTasks);
  grunt.initConfig(gruntConfig);

  grunt.event.on('watch', function (action, filepath) {
    // Only lint the file that actually changed
    grunt.config(['jshint', 'scripts'], filepath);
  });

  grunt.registerTask('test', [
    'concurrent:test', // browserify:test, copy:test
    'connect:test',
    'mocha_phantomjs'
  ]);

  grunt.registerTask('build', [
    'jshint',
    'concurrent:build', // browserify:build, compass:build, copy:build
    'connect:build'
  ]);

  grunt.registerTask('dist', [
    'build',
    'concurrent:dist', // htmlmin:dist, copy:dist, uglify, cssmin:dist
    'connect:dist'
  ]);

  grunt.registerTask('default', [
    'clean',
    'build',
    'test',
    'watch'
  ]);
};