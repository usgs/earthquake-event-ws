<?php

include_once '../conf/feeds.inc.php';


$query = new FDSNQuery();

try {

// format
	$format = param('format', null);

// eventid
	$eventid = param('eventid', null);

	if ($eventid === null) {
		throw new Exception("eventid is null");
	}
	$query->eventid = $eventid;


	if (!in_array($format, array('quakeml', 'geojson', 'geojsonp', 'kml',
				'kmlraw', 'xml'))) {
		throw new Exception('Unknown format "' . $format . '"');
	}

	if ($format === 'geojsonp') {
		$query->format = 'geojson';
		$query->callback = 'eqfeed_callback';
	} else {
		$query->format = $format;
	}

} catch (Exception $e) {

	if (($format === 'geojson' || $format === 'geojsonp') &&
			param('jsonerror', 'false') === 'true') {

		if ($format === 'geojsonp') {
			header('Content-type: text/javascript');
			echo 'eqfeed_callback(';
		} else {
			header('Content-type: application/json');
		}

		$response = array(
			'type' => 'Feature',
			'metadata' => array(
				'status' => 404,
				'generated' => time() . '000',
				'url' => $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'],
				'title' => 'Detail Error',
				'count' => 0,
				'error' => $e->getMessage()
			),
			'properties' => null
		);
		echo preg_replace('/"(generated)":"([\d]+)"/', '"$1":$2',
				str_replace('\/', '/', json_encode($response)));

		if ($format === 'geojsonp') {
			echo ');';
		}

	} else {
		header('HTTP/1.1 404 Not Found');
		header('Content-type: text/plain');
		echo "404 File Not Found\n";
		echo $e->getMessage();
		//trigger_error($e->getMessage());
	}

	exit();
}



// serve detail feed
$service = new FDSNEventWebService($fdsnIndex, true);
try {
	if ($query->format === 'quakeml' || $query->format === 'xml') {
		$service->handleSummaryQuery($query);
	} else {
		$service->handleDetailQuery($query);
	}
} catch (Exception $e) {
	$service->error(503, $e->getMessage());
}
