'use strict';

module.exports = function (grunt) {

  var gruntConfig = require('./gruntconfig');

  gruntConfig.tasks.forEach(grunt.loadNpmTasks);
  grunt.initConfig(gruntConfig);

  grunt.event.on('watch', function (action, filepath) {
    // Only lint the file that actually changed
    grunt.config(['jshint', 'scripts'], filepath);
  });

  grunt.registerTask('builddev', [
    'jshint',
    'browserify:build',
    'postcss:build',
    'copy:build'
  ]);

  grunt.registerTask('builddist', [
    'clean:dist',
    'copy:dist',
    'postcss:dist',
    'htmlmin',
    'uglify'
  ]);

  grunt.registerTask('buildtest', [
    'browserify:test',
    'browserify:bundle',
    'copy:test'
  ]);

  grunt.registerTask('default', [
    'dev',
    'test',
    'watch'
  ]);

  grunt.registerTask('dev', [
    'builddev',

    'configureProxies:build',
    'connect:template',
    'connect:build'
  ]);

  grunt.registerTask('dist', [
    'builddev',
    'builddist',

    'configureProxies:dist',
    'connect:template',
    'connect:dist:keepalive'
  ]);

  grunt.registerTask('test', [
    'buildtest',

    'connect:test',
    'mocha_phantomjs'
  ]);
};
