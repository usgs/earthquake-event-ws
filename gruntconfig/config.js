'use strict';

var BASE_PORT = 9060;


var config = {
  build: '.build',
  buildPort: BASE_PORT,
  dist: 'dist',
  distPort: BASE_PORT + 2,
  liveReloadPort: BASE_PORT + 9,
  src: 'src',
  templatePort: BASE_PORT + 8,
  test: 'test',
  testPort: BASE_PORT + 1
};


module.exports = config;
