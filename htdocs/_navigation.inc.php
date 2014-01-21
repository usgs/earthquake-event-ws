<?php

	include_once(dirname(__FILE__) . '/../conf/feed.inc.php');

	$feedSection = $FEED_HOST . $FEED_PATH;
	$fdsnSection = $FDSN_HOST . $FDSN_PATH;

	print side_nav_header();	
	print side_nav_group($feedSection . '/index.php', 'Formats',
		side_nav_item($feedSection . '/atom.php', 'ATOM') .
		side_nav_item($feedSection . '/geojson.php', 'GeoJSON Summary') .
		side_nav_item($feedSection . '/geojson_detail.php', 'GeoJSON Detail') .
		side_nav_item($feedSection . '/kml.php', 'KML') .
		side_nav_item($feedSection . '/csv.php', 'Spreadsheets') 
	);
	print side_nav_group($fdsnSection . '/', 'Web Service',
		side_nav_item($fdsnSection . '/', 'API Documentation') .
		side_nav_item($feedSection . '/urlbuilder.php', 'URL Builder')
	);
	print side_nav_item($feedSection . '/glossary.php', 'Glossary');
	print side_nav_item($feedSection . '/changelog.php', 'Change Log');
	print side_nav_item($feedSection . '/../policy.php', 'Feed Life Cycle Policy');
	print side_nav_footer();

?>
