'use strict';

var config = require('./config');


var htmlmin = {
  dist: {
    options: {
      removeComments: true,
      collapseWhitespace: true
    },
    files: [{
      expand: true,
      cwd: config.build + '/' + config.src,
      src: '**/*.html',
      dest: config.dist,
    }]
  }
};


module.exports = htmlmin;
