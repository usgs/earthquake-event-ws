'use strict';

var config = require ('./config');

var copy = {
  test: {
    files: [
      {
        expand: true,
        cwd: config.test,
        dest: config.build + '/' + config.test,
        src: [
          '**/*.html'
        ]
      }
    ]
  },
  build: {
    files: [
      {
        expand: true,
        cwd: config.src + '/htdocs',
        dest: config.build + '/' + config.src + '/htdocs',
        src: [
          'images/*.{png,gif,jpg,jpeg}',
          '**/*.html',
          '**/*.php'
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
          'images/region-controls.png'
        ],
      }
    ]
  },
  dist: {
    files: [
      {
        expand: true,
        cwd: config.build + '/' + config.src + '/',
        dest: config.dist + '/',
        src: [
          'images/*.{png,gif,jpg,jpeg}',
          '**/*.php'
        ]
      },
      {
        expand: true,
        cwd: config.build + '/' + config.src + '/',
        dest: config.dist + '/',
        src: [
          '**/*',
          '!**/*.orig'
        ]
      },
      {
        expand: true,
        cwd: config.build + '/' + config.src + '/',
        dest: config.dist + '/',
        src: [
          '**/*'
        ]
      }
    ]
  }
};

module.exports = copy;
