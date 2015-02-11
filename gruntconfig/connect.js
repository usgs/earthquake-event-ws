'use strict';

var config = require('./config');

var LIVE_RELOAD_PORT = 35729;
var lrSnippet = require('connect-livereload')({port: LIVE_RELOAD_PORT});
var gateway = require('gateway');
var rewriteModule = require('http-rewrite-middleware');

var iniConfig = require('ini').parse(require('fs')
    .readFileSync('./src/conf/config.ini', 'utf-8'));

var rewrites = [
  // Template
  {
    from: '^/theme/(.*)$',
    to: '/hazdev-template/dist/htdocs/$1'
  },

  // Search pages
  {
    from: '^' + iniConfig.SEARCH_PATH + '/$',
    to: '/search.php'
  },
  {
    from: '^' + iniConfig.SEARCH_PATH + '/(js|css|lib)/(.*)',
    to: '/$1/$2'
  },

  // Realtime feeds
  {
    from: '^' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION +
        '/detail/([^\\./]+)\\.([^\\./]+)$',
    to: '/detail.php?eventid=$1&format=$2'
  },
  {
    from: '^' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION +
        '/summary/([^/]+)\\.([^\\./]+)$',
    to: '/summary.php?params=$1&format=$2'
  },
  // Archive searches (with QSA essentially)
  {
    from: '^' + iniConfig.FDSN_PATH + '/query\\.([^/?]*)\\??(.*)$',
    to: '/fdsn.php?method=query&format=$1&$2'
  },
  {
    from: '^' + iniConfig.FDSN_PATH + '/([^/?]*)\\??(.*)$',
    to: '/fdsn.php?method=$1&$2'
  },

  // Other mount path stuff
  {
    from: '^' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION +
        '/(.*)$',
    to: '/$1'
  },
];

if (!iniConfig.hasOwnProperty('OFFSITE_HOST') ||
    iniConfig.OFFSITE_HOST.trim() !== '') {

  // Redirect for event page
  rewrites.push({
    from: '^' + iniConfig.EVENT_PATH + '(.*)$',
    to: iniConfig.OFFSITE_HOST + iniConfig.EVENT_PATH + '$1',
    redirect: 'permanent'
  });

  // Redirect for event content
  rewrites.push({
    from: '^' + iniConfig.storage_url + '(.*)$',
    to: iniConfig.OFFSITE_HOST + iniConfig.storage_url + '$1',
    redirect: 'permanent'
  });
}

var rewriteMiddleware = rewriteModule.getMiddleware(rewrites
    /*,{verbose:true}/**/);

// middleware to send CORS headers
var corsMiddleware = function (req, res, next) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', '*');
  res.setHeader('Access-Control-Allow-Headers', 'accept,origin,authorization,content-type');
  return next();
};

var mountPHP = function (dir, options) {
  options = options || {
    '.php': 'php-cgi',
    'env': {
      'PHPRC': process.cwd() + '/node_modules/hazdev-template/src/conf/php.ini'
    }
  };

  return gateway(require('path').resolve(dir), options);
};

var iniConfig = require('ini').parse(require('fs')
        .readFileSync('./src/conf/config.ini', 'utf-8'));

var connect = {
  options: {
    hostname: '*'
  },
  build: {
    options: {
      base: [
        config.build + '/' + config.src + '/htdocs',
        'node_modules'
      ],
      livereload: true,
      open: 'http://localhost:8000' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION + '/',
      port: 8000,
      middleware: function (connect, options) {
        return [
          lrSnippet,
          rewriteMiddleware,
          corsMiddleware,
          mountPHP(options.base[0])
        ];
      }
    }
  },
  test: {
    options: {
      base: [
        config.build + '/' + config.test,
        config.build + '/' + config.src + '/htdocs',
        'node_modules'
      ],
      open: 'http://localhost:8001/test.html',
      port: 8001
    }
  },
  dist: {
    options: {
      base: [
        config.dist + '/htdocs'
      ],
      keepalive: true,
      open: 'http://localhost:8002' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION + '/',
      port: 8002,
      middleware: function (connect, options) {
        return [
          rewriteMiddleware,
          corsMiddleware,
          mountPHP(options.base[0])
        ];
      }
    }
  }
};

module.exports = connect;