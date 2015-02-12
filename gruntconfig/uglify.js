'use strict';

var config = require('./config');

var uglify = {
  dist: {
    files: {}
  }
};

// uglify main bundle
uglify.dist.files[config.dist + '/htdocs/js/search.js'] =
    config.build + '/' + config.src + '/htdocs/js/search.js';

module.exports = uglify;