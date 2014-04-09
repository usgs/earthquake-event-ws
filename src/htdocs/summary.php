<?php

include_once '../conf/feeds.inc.php';

// requested url
$URL = $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'];

// name => age in seconds
$AGES = array(
	'hour' => 3600,
	'day' => 86400,
	'week' => 604800,
	'month' => 2592000
);

// name => size in magnitude
$SIZES = array(
	'all' => -1, 
	'1.0' => 1, 
	'2.5' => 2.5, 
	'4.5' => 4.5,
	'significant' => -1
);

$FORMATS = array('geojson', 'geojsonp', 'atom', 'kml', 'csv', 'quakeml');

$KML_COLORBYS = array('age', 'depth');





// parse arguments and build query
$query = new FDSNQuery();
$networkLink = false;

try {
	$params = param('params', false);
	$params = explode("_", $params);

// format
	$format = param('format', false);
	if (!$format || !in_array($format, $FORMATS)) {
		throw new Exception("Unknown format");
	}
	if ($format == 'geojsonp') {
		// geojsonp is geojson with implied callback name
		$query->format = 'geojson';
		$query->callback = 'eqfeed_callback';
	} else {
		$query->format = $format;
	}

// kml extras
	if ($format == 'kml') {
		// look for additional parameters
		$colorby = $params[2];
		if (!in_array($colorby, $KML_COLORBYS)) {
			throw new Exception("Unknown kml colorby");
		}
		$query->kmlcolorby = $colorby;
		// use magnitude bins, largest first
		$query->orderby = 'magnitude';

		// read optional kml params
		for($i=3, $len=count($params); $i<$len; $i++) {
			$param = $params[$i];

			if ($param === 'animated') {
				$query->kmlanimated = true;
			} else if ($param === 'link') {
				// serve network link to kml
				$networkLink = true;
			}
		}
	}

// age
	$age = $params[1];
	if (!isset($AGES[$age])) {
		throw new Exception("Unknown age");
	}
	// starttime is milliseconds
	$query->starttime = (time() - $AGES[$age]) . '000';


// size
	$size = $params[0];
	if (!isset($SIZES[$size])) {
		throw new Exception("Unknown size");
	}
	// size is either significance or magnitude
	if ($size === 'significant') {
		$query->minsig = 600;
	} else {
		$query->minmagnitude = $SIZES[$size];
	}


// caching headers
	// default to 5 minutes
	$CACHE_MAXAGE = 300;
	if ($age === 'month') {
		// all one month feeds are 15 minutes
		$CACHE_MAXAGE = 900;
	} else if ($query->format == 'geojson') {
		// geojson offers lower default expiration
		$CACHE_MAXAGE = 60;
	}
	include $APP_DIR . '/lib/cache.inc.php';


//title
	$title = 'USGS';
	if ($size == 'significant') {
		$title .= ' Significant';
	} else if ($size == 'all') {
		$title .= ' All';
	} else {
		$title .= ' Magnitude ' . $size . '+';
	}
	$title .= ' Earthquakes, Past ' . ucfirst($age);
	$query->resultTitle = $title;


} catch (Exception $e) {
	header('Status: 404 Not Found');
	header('Content-type: text/plain');
	echo '404 File Not Found';

	// log error
	trigger_error($e->getMessage());

	exit();
}



if ($networkLink) {
	// serve kml network link
	header('Content-type: application/vnd.google-earth.kml+xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
		'<kml xmlns="http://earth.google.com/kml/2.2">' .
		'<Document>' .
			'<name>Earthquakes</name>' .
			'<visibility>1</visibility>' .
			'<open>1</open>' .
			'<LookAt>' .
				'<longitude>-100</longitude>' .
				'<latitude>39</latitude>' .
				'<range>4000000</range>' .
				'<tilt>0</tilt>' .
				'<heading>0</heading>' .
			'</LookAt>' .
			'<NetworkLink>' .
				'<visibility>1</visibility>' .
				'<refreshVisibility>0</refreshVisibility>' .
				'<open>1</open>' .
				'<name>' . $query->resultTitle . '</name>' .
				'<Snippet maxLines="1">Updates every ' . intval($CACHE_MAXAGE / 60) . ' minutes</Snippet>' .
				'<Link>' .
					'<href>' . str_replace('_link', '', $URL) . '</href>' .
					'<refreshMode>onInterval</refreshMode>' .
					'<refreshInterval>' . $CACHE_MAXAGE . '</refreshInterval>' .
				'</Link>' .
			'</NetworkLink>' .
		'</Document>' .
		'</kml>';
	exit();
}



// serve summary feed
$service = new FDSNEventWebService($fdsnIndex);
try {
	$service->handleSummaryQuery($query);
} catch (Exception $e) {
	$service->error(503, $e->getMessage());
}
