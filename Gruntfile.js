'use strict';

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


var mountFolder = function (connect, dir) {
	return connect.static(require('path').resolve(dir));
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

module.exports = function (grunt) {

	// Load grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// App configuration, used throughout
	var appConfig = {
		src: 'src',
		dist: 'dist',
		test: 'test',
		tmp: '.tmp'
	};


	grunt.initConfig({
		app: appConfig,
		ini: iniConfig,
		watch: {
			scripts: {
				files: ['<%= app.src %>/htdocs/js/**/*.js'],
				tasks: ['concurrent:scripts'],
				options: {
					livereload: LIVE_RELOAD_PORT
				}
			},
			scss: {
				files: ['<%= app.src %>/htdocs/css/**/*.scss'],
				tasks: ['compass:dev']
			},
			tests: {
				files: ['<%= app.test %>/*.html', '<%= app.test %>/**/*.js'],
				tasks: ['concurrent:tests']
			},
			livereload: {
				options: {
					livereload: LIVE_RELOAD_PORT
				},
				files: [
					'<%= app.src %>/htdocs/**/*.html',
					'<%= app.src %>/htdocs/css/**/*.css',
					'<%= app.src %>/htdocs/img/**/*.{png,jpg,jpeg,gif}',
					'.tmp/css/**/*.css'
				]
			},
			gruntfile: {
				files: ['Gruntfile.js'],
				tasks: ['jshint:gruntfile']
			}
		},
		concurrent: {
			scripts: ['jshint:scripts', 'mocha_phantomjs'],
			tests: ['jshint:tests', 'mocha_phantomjs'],
			predist: [
				'jshint:scripts',
				'jshint:tests',
				'compass'
			],
			dist: [
				'requirejs:dist',
				'htmlmin:dist',
				'uglify',
				'copy'
			]
		},
		connect: {
			options: {
				hostname: 'localhost'
			},
			dev: {
				options: {
					base: '<%= app.src %>/htdocs',
					port: 8080,
					middleware: function (connect, options) {
						return [
							lrSnippet,
							rewriteMiddleware,
							corsMiddleware,
							mountFolder(connect, '.tmp'),
							mountPHP(options.base),
							mountFolder(connect, options.base),
							mountFolder(connect, 'node_modules')
						];
					}
				}
			},
			dist: {
				options: {
					base: '<%= app.dist %>/htdocs',
					port: 8081,
					keepalive: true,
					middleware: function (connect, options) {
						return [
							rewriteMiddleware,
							corsMiddleware,
							mountPHP(options.base),
							mountFolder(connect, options.base),
							mountFolder(connect, '.tmp'),
							mountFolder(connect, 'node_modules')
						];
					}
				}
			},
			test: {
				options: {
					base: '<%= app.test %>',
					port: 8000,
					middleware: function (connect, options) {
						return [
							mountFolder(connect, '.tmp'),
							mountFolder(connect, 'node_modules'),
							mountFolder(connect, options.base),
							mountFolder(connect, appConfig.src + '/htdocs/js')
						];
					}
				}
			}
		},
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			gruntfile: ['Gruntfile.js'],
			scripts: ['<%= app.src %>/htdocs/js/**/*.js'],
			tests: ['<%= app.test %>/**/*.js']
		},
		compass: {
			dev: {
				options: {
					sassDir: '<%= app.src %>/htdocs/css',
					cssDir: '<%= app.tmp %>/css',
					environment: 'development'
				}
			}
		},
		mocha_phantomjs: {
			all: {
				options: {
					urls: [
						'http://localhost:<%= connect.test.options.port %>/index.html'
					]
				}
			}
		},
		requirejs: {
			dist: {
				options: {
					name: 'search',
					baseUrl: appConfig.src + '/htdocs/js',
					out: appConfig.dist + '/htdocs/js/search.js',
					optimize: 'uglify2',
					mainConfigFile: appConfig.src + '/htdocs/js/search.js',
					useStrict: true,
					wrap: true,
					uglify2: {
						report: 'gzip',
						mangle: true,
						compress: true,
						preserveComments: 'some'
					},
					paths: {
						leaflet: '../../../node_modules/leaflet/dist/leaflet-src',
						locationview: '../../../node_modules/hazdev-location-view/src',
						mvc: '../../../node_modules/hazdev-webutils/src/mvc',
						util: '../../../node_modules/hazdev-webutils/src/util'
					},
					shim: {
						leaflet: {
							exports: 'L'
						}
					}
				}
			}
		},
		cssmin: {
			dist: {
				options: {
					root: 'node_modules'
				},
				files: {
					'<%= app.dist %>/htdocs/css/index.css': [
						'.tmp/css/index.css'
					],
					'<%= app.dist %>/htdocs/css/search.css': [
						'<%= app.dist %>/htdocs/css/search.css'
					],
					'<%= app.dist %>/htdocs/css/feedPages.css': [
						'.tmp/css/feedPages.css'
					],
					'<%= app.dist %>/htdocs/css/glossary.css': [
						'.tmp/css/glossary.css'
					]
				}
			}
		},
		htmlmin: {
			dist: {
				options: {
					collapseWhitespace: true
				},
				files: [{
					expand: true,
					cwd: '<%= app.src %>',
					src: '**/*.html',
					dest: '<%= app.dist %>'
				}]
			}
		},
		uglify: {
			options: {
				mangle: true,
				compress: true,
				report: 'gzip'
			},
			dist: {
				files: {
					'<%= app.dist %>/htdocs/lib/requirejs/require.js':
							['node_modules/requirejs/require.js'],
				}
			}
		},
		copy: {
			app: {
				expand: true,
				cwd: '<%= app.src %>/htdocs',
				dest: '<%= app.dist %>/htdocs',
				src: [
					'images/*.{png,gif,jpg,jpeg}',
					'**/*.php'
				]
			},
			conf: {
				expand: true,
				cwd: '<%= app.src %>/conf',
				dest: '<%= app.dist %>/conf',
				src: [
					'**/*',
					'!**/*.orig'
				]
			},
			lib: {
				expand: true,
				cwd: '<%= app.src %>/lib',
				dest: '<%= app.dist %>/lib',
				src: [
					'**/*'
				]
			},
			css: {
				expand: true,
				cwd: '<%= app.tmp %>/css',
				dest: '<%= app.dist %>/htdocs/css',
				src: [
					'search.css'
				],
				options: {
					process: function (content, srcpath) {
						return content.replace(
								'@import url(/hazdev-webutils/src/ModalView.css);',
								'');
					}
				}
			}
		},
		replace: {
			dist: {
				src: [
					'<%= app.dist %>/htdocs/index.html',
					'<%= app.dist %>/**/*.php'
				],
				overwrite: true,
				replacements: [
					{
						from: 'requirejs/require.js',
						to: 'lib/requirejs/require.js'
					},
					{
						from: 'html5shiv-dist/html5shiv.js',
						to: 'lib/html5shiv/html5shiv.js'
					}
				]
			}
		},
		open: {
			dev: {
				path: 'http://localhost:<%= connect.dev.options.port %>' +
						iniConfig.FEED_PATH + '/' + iniConfig.API_VERSION + '/'
			},
			test: {
				path: 'http://localhost:<%= connect.test.options.port %>'
			},
			dist: {
				path: 'http://localhost:<%= connect.dist.options.port %>'
			}
		},
		clean: {
			dist: ['<%= app.dist %>'],
			dev: ['<%= app.tmp %>', '.sass-cache']
		}
	});

	grunt.event.on('watch', function (action, filepath) {
		// Only lint the file that actually changed
		grunt.config(['jshint', 'scripts'], filepath);
	});

	grunt.registerTask('test', [
		'clean:dist',
		'connect:test',
		'mocha_phantomjs'
	]);

	grunt.registerTask('build', [
		'clean:dist',
		'concurrent:predist',
		'concurrent:dist',
		'cssmin:dist',
		'replace',
		'open:dist',
		'connect:dist'
	]);

	grunt.registerTask('default', [
		'clean:dist',
		'compass:dev',
		'connect:test',
		'connect:dev',
		'open:test',
		'open:dev',
		'watch'
	]);

};
