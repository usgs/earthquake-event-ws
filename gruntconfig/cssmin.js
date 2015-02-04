'use strict';

var config = require('./config');

var cssmin = {
  dist: {
    cwd: config.build + '/' + config.src,
    expand: true,
    dest: config.dist,
    src: [
      '**/*.css'
    ]
  }
};

module.exports = cssmin;