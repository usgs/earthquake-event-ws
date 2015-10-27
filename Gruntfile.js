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
    'browserify:test',
    'browserify:bundle',
    'copy:test',
    'connect:test',
    'mocha_phantomjs'
  ]);

  grunt.registerTask('dev', [
    'build',
    'connect:template',
    'configureProxies:build',
    'connect:build'
  ]);

  grunt.registerTask('build', [
    'jshint',
    'browserify:build',
    'postcss:build',
    'copy:build'
  ]);

  grunt.registerTask('dist', [
    'clean:dist',
    'build',
    'copy:dist',
    'postcss:dist',
    'htmlmin',
    'uglify',
    'connect:template',
    'configureProxies:dist',
    'connect:dist:keepalive'
  ]);

  grunt.registerTask('default', [
    'clean',
    'dev',
    'test',
    'watch'
  ]);
};
