<?php

include_once '../conf/feed.inc.php';


$query = new FDSNQuery();

try {

// eventid
	$eventid = param('eventid', null);
	if ($eventid === null) {
		throw new Exception("eventid is null");
	}
	$query->eventid = $eventid;


// format
	$format = param('format', null);
	if (!in_array($format, array('quakeml', 'geojson', 'geojsonp', 'kml', 'kmlraw'))) {
		throw new Exception('Unknown format "' . $format . '"');
	}
	if ($format === 'geojsonp') {
		$query->format = 'geojson';
		$query->callback = 'eqfeed_callback';
	} else {
		$query->format = $format;
	}

} catch (Exception $e) {
	header('Status: 404 Not Found');
	header('Content-type: text/plain');
	echo '404 File Not Found';

	// log error
	trigger_error($e->getMessage());

	exit();
}



// serve detail feed
$service = new FDSNEventWebService($fdsnIndex);
try {
	if ($query->format === 'quakeml') {
		$service->handleSummaryQuery($query);
	} else {
		$service->handleDetailQuery($query);
	}
} catch (Exception $e) {
	$service->error(503, $e->getMessage());
}
