<?php
	include_once '../conf/config.inc.php';

	$feedSection = $FEED_HOST . $FEED_PATH;
	$fdsnSection = $FDSN_HOST . $FDSN_PATH;

	print navGroup('Formats',
		navItem($feedSection . '/index.php', 'Summary') .
		navItem($feedSection . '/atom.php', 'ATOM') .
		navItem($feedSection . '/geojson.php', 'GeoJSON Summary') .
		navItem($feedSection . '/geojson_detail.php', 'GeoJSON Detail') .
		navItem($feedSection . '/kml.php', 'KML') .
		navItem($feedSection . '/csv.php', 'Spreadsheets') .
		navItem($feedSection . '/quakeml.php', 'QuakeML')
	);
	print navGroup('Web Service',
		navItem($fdsnSection . '/', 'API Documentation') .
		navItem($SEARCH_PATH . '/', 'Search Earthquake Archives')
	);
	print navItem($feedSection . '/glossary.php', 'Glossary');
	print navItem($feedSection . '/changelog.php', 'Change Log');
	print navItem($feedSection . '/../policy.php', 'Feed Life Cycle Policy');

?>
