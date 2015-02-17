'use strict';

var config = require('./config');

var browserify = {
  options: {
    browserifyOptions: {
      debug: true,
      paths: [
        process.cwd() + '/' + config.src,
        process.cwd() + '/node_modules',
        process.cwd() + '/node_modules/hazdev-location-view/src',
        process.cwd() + '/node_modules/hazdev-webutils/src',
        process.cwd() + '/node_modules/hazdev-template/dist'
      ]
    }
  },
  build: {
    src: config.src + '/htdocs/js/search.js',
    dest: config.build + '/' + config.src + '/htdocs/js/search.js',
    options: {
      alias: [
        './' + config.src + '/htdocs/js/search.js:js/search'
      ]
    }
  },
  test: {
    src: config.test + '/test.js',
    dest: config.build + '/' + config.test + '/test.js',
    options: {
      external: [
        'js/search'
      ]
    }
  }
};

module.exports = browserify;