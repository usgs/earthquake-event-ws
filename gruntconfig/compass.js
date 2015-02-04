'use strict';

var config = require('./config');

var compass = {
  build: {
    options: {
      sassDir: config.src + '/htdocs/css',
      specify: config.src + '/htdocs/css/earthquake-event-ws.scss',
      cssDir: config.build + '/' + config.src + '/htdocs/css',
      environment: 'development'
    }
  }
};

module.exports = compass;