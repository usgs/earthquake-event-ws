'use strict';

var config = require('./config');

var compass = {
  build: {
    options: {
      importPath: [
        'node_modules/hazdev-location-view/src',
        'node_modules/hazdev-template/src/htdocs', // earthquake variables
        'node_modules/hazdev-webutils/src'
      ],
      sassDir: config.src + '/htdocs/css',
      specify: config.src + '/htdocs/css/*.scss',
      cssDir: config.build + '/' + config.src + '/htdocs/css',
      environment: 'development'
    }
  }
};

module.exports = compass;