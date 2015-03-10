'use strict';

var config = require('./config');

var uglify = {
  dist: {
    src: config.build + '/' + config.src + '/htdocs/js/search.js',
    dest: config.dist + '/htdocs/js/search.js'
  }
};

module.exports = uglify;