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
        process.cwd() + '/node_modules/hazdev-webutils/src'
      ]
    }
  },
  source: {
    src: config.src + '/htdocs/js/search.js',
    dest: config.build + '/' + config.src + '/htdocs/js/earthquake-event-ws.js',
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

/*
    requirejs: {
      dist: {
        options: {
          name: 'search',
          baseUrl: appConfig.src + '/htdocs/js',
          out: appConfig.dist + '/htdocs/js/search.js',
          optimize: 'uglify2',
          mainConfigFile: appConfig.src + '/htdocs/js/search.js',
          useStrict: true,
          wrap: true,
          uglify2: {
            report: 'gzip',
            mangle: true,
            compress: true,
            preserveComments: 'some'
          },
          paths: {
            leaflet: '../../../node_modules/leaflet/dist/leaflet-src',
            locationview: '../../../node_modules/hazdev-location-view/src',
            mvc: '../../../node_modules/hazdev-webutils/src/mvc',
            util: '../../../node_modules/hazdev-webutils/src/util'
          },
          shim: {
            leaflet: {
              exports: 'L'
            }
          }
        }
      }
    },
*/