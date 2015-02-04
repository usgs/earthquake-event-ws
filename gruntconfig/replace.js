'use strict';

var config = require('./config');

var replace = {
  dist: {
    src: [
      config.dist + '/htdocs/index.html',
      config.dist + '/**/*.php'
    ],
    overwrite: true,
    replacements: [
      // {
      //   from: 'requirejs/require.js',
      //   to: 'lib/requirejs/require.js'
      // },
      {
        from: 'html5shiv-dist/html5shiv.js',
        to: 'lib/html5shiv/html5shiv.js'
      }
    ]
  }
};

module.exports = replace;