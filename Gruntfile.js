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
    'copy:test',
    'connect:test',
    'mocha_phantomjs'
  ]);

  grunt.registerTask('build', [
    'browserify',
    'concurrent:predist', /// jshint:scripts, jshint:test, compass
    'compass:build',
    'copy:build',
    'replace',
    'connect:build'
  ]);

  grunt.registerTask('dist', [
    'build',
    'concurrent:dist', /// htmlmin:dist, copy:dist, uglify
    'cssmin:dist',
    'connect:dist'
  ]);

  grunt.registerTask('default', [
    'clean',
    'build',
    'test',
    'watch'
  ]);
};