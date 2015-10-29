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
    options: {
      mode: true
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
