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

  // app: {
  //   expand: true,
  //   cwd: config.src + '/htdocs',
  //   dest: config.build + '/' + config.src + '/htdocs',
  //   src: [
  //     'images/*.{png,gif,jpg,jpeg}',
  //     '**/*.php'
  //   ]
  // },
  // conf: {
  //   expand: true,
  //   cwd: config.src + '/conf',
  //   dest: config.build + '/' + config.src + '/conf',
  //   src: [
  //     '**/*',
  //     '!**/*.orig'
  //   ]
  // },
  // lib: {
  //   expand: true,
  //   cwd: config.src + '/lib',
  //   dest: config.build + '/' + config.src + '/lib',
  //   src: [
  //     '**/*'
  //   ]
  // },
  // css: {
  //   expand: true,
  //   cwd: config.src + '/css',
  //   dest: config.build + '/' + config.src + '/htdocs/css',
  //   src: [
  //     'search.css'
  //   ]
  //   options: {
  //     process: function (content, srcpath) {
  //       return content.replace(
  //           '@import url(/hazdev-webutils/src/ModalView.css);',
  //           '');
  //     }
  //   }
  // }

module.exports = copy;