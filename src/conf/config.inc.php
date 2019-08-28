<?php

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  // short-circuit OPTIONS method requests, which are CORS related
  exit();
}

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

// read the regular app config
$APP_DIR = dirname(dirname(__FILE__));
// configuration parameters read from config.ini
$CONFIG = parse_ini_file($APP_DIR . '/conf/config.ini');
$APP_NAME = basename($APP_DIR);
$MOUNT_PATH = $CONFIG['FEED_PATH'];
$EVENT_PATH = $CONFIG['EVENT_PATH'];

$MAX_SEARCH = array_key_exists('MAX_SEARCH', $CONFIG) ?
    $CONFIG['MAX_SEARCH'] : null;
$PRODUCT_MAX_SEARCH = array_key_exists('PRODUCT_MAX_SEARCH', $CONFIG) ?
    $CONFIG['PRODUCT_MAX_SEARCH'] : null;

$API_VERSION = $CONFIG['API_VERSION'];

$DETAIL_PATH  = $CONFIG['FEED_PATH'] . '/' . $API_VERSION . '/detail/';
$SUMMARY_PATH   = $CONFIG['FEED_PATH'] . '/' . $API_VERSION . '/summary/';
$FEED_PATH     = $CONFIG['FEED_PATH']    . '/' . $API_VERSION;

$SEARCH_PATH = $CONFIG['SEARCH_PATH'];

$FDSN_PATH = $CONFIG['FDSN_PATH'];
// version is replaced using package.json version by grunt copy task
$FDSN_VERSION = '{{VERSION}}';

$PRODUCT_PATH = $CONFIG['PRODUCT_PATH'];

$MAPLIST_PATH = $CONFIG['MAPLIST_PATH'];

$DEFAULT_MAXEVENTAGE = isset($CONFIG['DEFAULT_MAXEVENTAGE']) ?
    $CONFIG['DEFAULT_MAXEVENTAGE'] : null;

$SCENARIO_MODE = false;
if (isset($CONFIG['INSTALLATION_TYPE']) &&
    strcasecmp($CONFIG['INSTALLATION_TYPE'], 'scenario') === 0) {
    $SCENARIO_MODE = true;
}

$storage_directory = $CONFIG['storage_directory'];
$storage_url = $CONFIG['storage_url'];

$forwarded_https = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// build absolute Event Page URL string
$server_protocol =
    (
      (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'Off')
      || $forwarded_https
    ) ? 'https://' : 'http://';
$server_host = isset($_SERVER['HTTP_HOST']) ?
    $_SERVER['HTTP_HOST'] : "earthquake.usgs.gov";
$server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
$server_uri = $_SERVER['REQUEST_URI'];

$HOST_URL_PREFIX = $server_protocol . $server_host;
if ( ($server_port == 80 && ($server_protocol == 'http://' || $forwarded_https))
    || ($server_port == 443 && $server_protocol == 'https://') ) {
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
$PRODUCT_HOST = '';
$FEED_URL = $FEED_HOST . $FEED_PATH;
$FDSN_URL = $FDSN_HOST . $FDSN_PATH;
$PRODUCT_URL = $PRODUCT_HOST . $PRODUCT_PATH;