'use strict';

var config = require('./config');

var gateway = require('gateway');
var rewriteModule = require('http-rewrite-middleware');

var iniConfig = require('ini').parse(require('fs')
    .readFileSync('./src/conf/config.ini', 'utf-8'));


var proxies = [
  {
    context: '/theme/',
    host: 'localhost',
    port: config.templatePort,
    rewrite: {
      '^/theme': ''
    }
  }
];


var rewrites = [

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
  {
    from: '^' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION +
        '/shakealert.geojson',
    to: '/shakealert.php'
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
  {
    from: '^' + iniConfig.PRODUCT_PATH + '/$',
    to: '/product.php'
  },

  // Other mount path stuff
  {
    from: '^' + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION +
        '/(.*)$',
    to: '/$1'
  },
];

if (iniConfig.hasOwnProperty('OFFSITE_HOST') &&
    iniConfig.OFFSITE_HOST.trim() !== '') {

  // Redirect for event page
  rewrites.push({
    from: '^' + iniConfig.EVENT_PATH + '(.*)$',
    to: iniConfig.OFFSITE_HOST + iniConfig.EVENT_PATH + '$1',
    redirect: 'permanent'
  });

  proxies.push({
    context: iniConfig.storage_url,
    headers: {
      'host': iniConfig.OFFSITE_HOST,
      'accept-encoding': 'identity'
    },
    host: iniConfig.OFFSITE_HOST,
    port: 80
  });
}

var rewriteMiddleware = rewriteModule.getMiddleware(rewrites
    /*,{verbose:true}*/);

var proxyMiddleware = require('grunt-connect-proxy/lib/utils').proxyRequest;

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
      'PHPRC': process.cwd() + '/node_modules/hazdev-template/dist/conf/php.ini'
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

  proxies: proxies,

  build: {
    options: {
      base: [
        config.build + '/' + config.src + '/htdocs'
      ],
      livereload: config.liveReloadPort,
      open: 'http://localhost:' + config.buildPort + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION + '/',
      port: config.buildPort,
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(
          corsMiddleware,
          rewriteMiddleware,
          proxyMiddleware,
          mountPHP(options.base[0])
        );
        return middlewares;
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
      open: 'http://localhost:' + config.testPort + '/test.html',
      port: config.testPort,
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(
          corsMiddleware,
          rewriteMiddleware,
          proxyMiddleware,
          mountPHP(options.base[0])
        );
        return middlewares;
      }
    }
  },

  dist: {
    options: {
      base: [
        config.dist + '/htdocs'
      ],
      keepalive: true,
      open: 'http://localhost:' + config.distPort + iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION + '/',
      port: config.distPort,
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(
          corsMiddleware,
          rewriteMiddleware,
          proxyMiddleware,
          mountPHP(options.base[0])
        );
        return middlewares;
      }
    }
  },

  template: {
    options: {
      base: ['node_modules/hazdev-template/dist/htdocs'],
      middleware: function (connect, options, middlewares) {
        middlewares.unshift(mountPHP(options.base[0]));
        return middlewares;
      },
      port: config.templatePort
    }
  }
};


module.exports = connect;
