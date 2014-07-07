<?php
if (!isset($TEMPLATE)) {
	include_once '../conf/config.inc.php';
	$TITLE = 'Feeds &amp; Notifications';
	$HEAD = '<link rel="stylesheet" href="/css/imagelist.css"/>';
	include 'classes/ImageList.class.php';
	include 'template.inc.php';
}
?>


<div class="row">

	<div class="one-of-two column">
	<h2>Real-time Feeds</h2>
<?php
$list = new ImageList();

$list->addItem(
		'atom.php', //link
		'images/atom.png', //image
		'ATOM Syndication', //title
		'<p>A basic syndication format supported by a variety of feed readers.
			This is a good option for casually subscribing to earthquake
			information.</p>'); //content

$list->addItem(
		'kml.php', //link
		'images/kml.png', //image
		'Google Earth&trade; KML', //title
		'<p>This feed format is suitable for loading into applications that
			understand Keyhole Markup Language (KML) such as Google
			Earth&trade;</p>'); //content
	/*
		Icon Source: www.iconspedia.com
		Author: Tempest, http://tempest.deviantart.com/
		License: CC Attribution Non-Commercial No Derivatives
	*/

$list->addItem(
		'csv.php', //link
		'images/csv.png', //image
		'Spreadsheet Format', //title
		'<p>A simple text format suitable for loading data into spreadsheet
			applications like Microsoft Excel&trade;. This is a good option for
			manual scientific analysis.</p>'); //content

$list->addItem(
		'quakeml.php', //link
		'images/quakeml.png', //image
		'QuakeML', //title
		'<p>A flexible, extensible and modular XML representation of
			seismological data which is intended to cover a broad range of
			fields of application in modern seismology.</p>'); //content

	$list->display();
?>
	</div>


	<div class="one-of-two column">
	<h2>Real-time Notifications</h2>
<?php
$list = new ImageList();

$list->addItem(
		'https://sslearthquake.usgs.gov/ens/', //link
		'images/ens-x2.png', //image
		'Earthquake Notification Service', //title
		'<p>The Earthquake Notification Service (ENS) is a free service that
			sends you automated notifications to your email or cell phone when
			earthquakes happen.</p>'); //content

$list->addItem(
		'/earthquakes/ted/', //link
		'images/ted.png', //image
		'Tweet Earthquake Dispatch', //title
		'<p>Tweet Earthquake Dispatch (TED) offers two Twitter accounts. On
			average, each account will produce about one tweet per day.</p>'); //content

	$list->display();
?>
	</div>

</div>


<div class="row">
<h2>For Developers</h2>
	<div class="one-of-two column">
		<ul>
			<li><a href="<?php print $FDSN_URL;?>/">API Documentation - EQ Catalog</a></li>
			<li><a href="<?php print $FEED_URL;?>/geojson.php">GeoJSON Summary Feed</a></li>
			<li><a href="<?php print $FEED_URL;?>/geojson_detail.php">GeoJSON Detail Feed</a></li>
			<li><a href="<?php print $FEED_URL;?>/changelog.php">Change Log</a></li>
			<li><a href="<?php print $FEED_URL;?>/../policy.php">Feed Lifecycle Policy</a></li>
		</ul>
	</div>


	<div class="one-of-two column">
		<ul>
			<li><a href="https://github.com/usgs/devcorner">Developers Corner</a></li>
			<li><a href="<?php print $FEED_URL;?>/glossary.php">Glossary - Earthquake Catalog Data Terms</a></li>
			<li><a href="https://geohazards.usgs.gov/mailman/listinfo/realtime-feeds">Mailing List - Announcements</a></li>
			<li><a href="https://geohazards.usgs.gov/mailman/listinfo/realtime-feed-users">Mailing List - Forum/Questions</a></li>
		</ul>
	</div>

</div>