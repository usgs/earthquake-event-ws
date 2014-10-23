<?php

/**
 * This file handles caching headers.
 *
 * Scripts that include this file should set "template" variables
 *
 * $MODIFIED - when the page was last modified, default is time().
 * $CACHE_MAXAGE - maximum age in seconds, default is 900 (15 minutes).
 * $CACHE_SCOPE - whether this can be publicly or privately cached, default is "public".
 */

// phps "r" date format puts +0000 for timezone
define('RFC_DATE', 'D, d M Y H:i:s \G\M\T');


// set defaults
if (!isset($MODIFIED)) {
	$MODIFIED = time();
}
if (!isset($CACHE_MAXAGE)) {
	$CACHE_MAXAGE = 900;
}
if (!isset($CACHE_SCOPE)) {
	$CACHE_SCOPE = "public";
}


if ($CACHE_MAXAGE >= 0) {
	// do these before if-modified-since, so that response can also be cached
	header('Cache-Control: ' . $CACHE_SCOPE . ', max-age=' . $CACHE_MAXAGE);
	header('Expires: ' . gmdate(RFC_DATE, time() + $CACHE_MAXAGE));
}


// stop processing if not modified
/*
	if-modified-since will be handled by caching servers.
	app always serves feed.

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
	$since = strtotime(trim(strstr($_SERVER['HTTP_IF_MODIFIED_SINCE'], ':')));

	if ($since >= $MODIFIED) {
		// not modified
		header('HTTP/1.1 304 Not Modified');
		return;
	}
}
*/


// make this cacheable
header('Last-Modified: ' . gmdate(RFC_DATE, $MODIFIED));
