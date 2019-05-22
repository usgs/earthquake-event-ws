'use strict';


var config = require ('./config'),
    fs = require('fs'),
    packageJson;

// read package.json
packageJson = JSON.parse(fs.readFileSync('package.json'));


var copy = {
  test: {
    files: [
      {
        expand: true,
        cwd: config.test,
        dest: config.build + '/' + config.test,
        src: [
          '**/*.html',
          '**/*.php'
        ]
      }
    ]
  },

  build: {
    options: {
      mode: true,
      noProcess: ['**/*.{gif,ico,jpg,png,tif,pdf,mp4,kmz,gz,zip}'],
      process: function (content/*, srcpath*/) {
        // replace {{VERSION}} in php/html with version from package.json
        return content.replace('{{VERSION}}', packageJson.version);
      }
    },
    files: [
      {
        expand: true,
        cwd: config.src + '/htdocs',
        dest: config.build + '/' + config.src + '/htdocs',
        src: [
          'images/*.{png,gif,jpg,jpeg}',
          '**/*.html',
          '**/*.php',
          '**/*.kmz'
        ]
      },
      {
        expand: true,
        cwd: config.src + '/conf',
        dest: config.build + '/' + config.src + '/conf',
        src: [
          '**/*',
          '!**/*.orig'
        ]
      },
      {
        expand: true,
        cwd: config.src + '/lib',
        dest: config.build + '/' + config.src + '/lib',
        src: [
          '**/*'
        ]
      },
      {
        expand: true,
        cwd: 'node_modules/leaflet/dist',
        dest: config.build + '/' + config.src + '/htdocs/css',
        src: [
          'leaflet.css'
        ]
      },
      {
        expand: true,
        cwd: 'node_modules/hazdev-location-view/src/locationview',
        dest: config.build + '/' + config.src + '/htdocs/css',
        src: [
          'images/region-control-icons.png'
        ],
      }
    ]
  },

  dist: {
    options: {
      mode: true
    },
    files: [
      {
        expand: true,
        cwd: config.build + '/' + config.src + '/',
        dest: config.dist + '/',
        src: [
          '**/*',
          '!**/*.orig'
        ]
      }
    ]
  }
};


module.exports = copy;
