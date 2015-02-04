'use strict';

var config = require('./config');

var uglify = {
  dist: {
    files: {}
  }
};

// uglify main bundle
uglify.dist.files[config.dist + '/htdocs/js/earthquake-event-ws.js'] =
    config.build + '/' + config.src + '/htdocs/js/earthquake-event-ws.js';

module.exports = uglify;