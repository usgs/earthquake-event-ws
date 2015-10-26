'use strict';

var config = require('./config');

var EXPORTS = [
  './' + config.src + '/htdocs/js/fdsn/EventTypeField.js:fdsn/EventTypeField',
  './' + config.src + '/htdocs/js/fdsn/FDSNModel.js:fdsn/FDSNModel',
  './' + config.src + '/htdocs/js/fdsn/FDSNModelValidator.js:fdsn/FDSNModelValidator',
  './' + config.src + '/htdocs/js/fdsn/FDSNSearchForm.js:fdsn/FDSNSearchForm',
  './' + config.src + '/htdocs/js/fdsn/ManagedModelView.js:fdsn/ManagedModelView',
  './' + config.src + '/htdocs/js/fdsn/SelectField.js:fdsn/SelectField',
  './' + config.src + '/htdocs/js/fdsn/ToggleSection.js:fdsn/ToggleSection',
  './' + config.src + '/htdocs/js/fdsn/UrlBuilderFormatter.js:fdsn/UrlBuilderFormatter',
  './' + config.src + '/htdocs/js/fdsn/UrlManager.js:fdsn/UrlManager'
];


var browserify = {
  options: {
    browserifyOptions: {
      debug: true,
      paths: [
        process.cwd() + '/' + config.src,
        process.cwd() + '/' + config.src + '/htdocs/js/',
        process.cwd() + '/node_modules',
        process.cwd() + '/node_modules/hazdev-location-view/src',
        process.cwd() + '/node_modules/hazdev-webutils/src',
        process.cwd() + '/node_modules/hazdev-template/dist'
      ]
    }
  },

  bundle: {
    src: [],
    dest: config.build + '/' + config.test + '/earthquake-event-ws.js',
    options: {
      alias: EXPORTS
    }
  },

  build: {
    src: config.src + '/htdocs/js/search.js',
    dest: config.build + '/' + config.src + '/htdocs/js/search.js'
  },

  test: {
    src: config.test + '/test.js',
    dest: config.build + '/' + config.test + '/test.js',
    options: {
      external: EXPORTS
    }
  }
};


module.exports = browserify;
