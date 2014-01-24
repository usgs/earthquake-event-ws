<?php

// use UTC timezone for date parsing/formatting
date_default_timezone_set("UTC");


$TEMPLATE_FUNCTIONS = $_SERVER['DOCUMENT_ROOT'] . '/template/static/functions.inc.php';
if (file_exists($TEMPLATE_FUNCTIONS)) {
	include_once($TEMPLATE_FUNCTIONS);
} else if (!function_exists('param')) {
	function param($name, $default=null) {
		if (isset($_POST[$name])) {
			return $_POST[$name];
		} else if (isset($_GET[$name])) {
			return $_GET[$name];
		}
		return $default;
	}
}


// LOAD LIBRARY CLASSES
include_once(dirname(__FILE__) . '/../lib/lib.inc.php');


// read the regular app config
$APP_DIR = dirname(dirname(__FILE__));
// configuration parameters read from config.ini
$CONFIG = parse_ini_file($APP_DIR . '/conf/config.ini');
$APP_NAME = basename($APP_DIR);
$MOUNT_PATH = $CONFIG['FEED_PATH'];
$EVENT_PATH = $CONFIG['EVENT_PATH'];

$MAX_SEARCH = array_key_exists('MAX_SEARCH', $CONFIG) ?
		$CONFIG['MAX_SEARCH'] : null;

$API_VERSION = $CONFIG['API_VERSION'];

$DETAIL_PATH	= $CONFIG['FEED_PATH'] . '/' . $API_VERSION . '/detail/';
$SUMMARY_PATH 	= $CONFIG['FEED_PATH'] . '/' . $API_VERSION . '/summary/';
$FEED_PATH 		= $CONFIG['FEED_PATH'] 	 . '/' . $API_VERSION;

$SEARCH_PATH = $CONFIG['SEARCH_PATH'];

$FDSN_PATH = $CONFIG['FDSN_PATH'];
$FDSN_VERSION = $CONFIG['FDSN_VERSION'];

$DEFAULT_MAXEVENTAGE = isset($CONFIG['DEFAULT_MAXEVENTAGE']) ?
		$CONFIG['DEFAULT_MAXEVENTAGE'] : null;


$storage_directory = $CONFIG['storage_directory'];
$storage_url = $CONFIG['storage_url'];

// build absolute Event Page URL string
$server_protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$server_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "earthquake.usgs.gov";
$server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
$server_uri = $_SERVER['REQUEST_URI'];

$HOST_URL_PREFIX = $server_protocol . $server_host;
if ( ($server_port == 80 && $server_protocol == 'http://') || ($server_port == 443 && $server_protocol == 'https://') ) {
	// don't need port
} else {
	// if a port is specified in the HTTP_HOST, don't use twice (ex: localhost:8080, perhaps used in port forwarding)
	if(!strpos($server_host, ':')) {
		$HOST_URL_PREFIX .= ':' . $server_port;
	}
}


// by default searches and feeds are local
$FDSN_HOST = '';
$FEED_HOST = '';
if (strpos($HOST_URL_PREFIX, 'earthquake.usgs.gov') !== FALSE) {
	// on earthquake, searches are on comcat
	$FDSN_HOST = 'http://comcat.cr.usgs.gov';
}
if (strpos($HOST_URL_PREFIX, 'comcat') !== FALSE) {
	// on comcat, feeds are on earthquake
	$FEED_HOST = 'http://earthquake.usgs.gov';
}


// create the ProductStorage instance with the absolute URL address
$storage = new ProductStorage($storage_directory, $HOST_URL_PREFIX . $storage_url);

// create the ProductIndex instance with the absolute URL address
$index = new ProductIndex( $HOST_URL_PREFIX . $CONFIG['EVENT_PATH']);
$index->connect($CONFIG['db_hostname'],
		$CONFIG['db_read_user'],
		$CONFIG['db_read_pass'],
		$CONFIG['db_name']
		);

// reuse pdo connection for fdsn index
$fdsnIndex = new FDSNIndex();
$fdsnIndex->pdo = $index->connection;
