<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'Real-time Feeds &amp; Notifications';
	$NAVIGATION = true;
	$HEAD = '
		<link rel="stylesheet" href="css/index.css"/>
	';

	include 'template.inc.php';
}
?>

<div class="row">
	<div class="one-of-two column">
		<a href="/earthquakes/ted/" id="ted" class="format"
				title="Tweet Earthquake Dispatch">
			<h2>Tweet Earthquake Dispatch</h2>
			<p>
				Tweet Earthquake Dispatch (TED) offers two Twitter accounts. On
				average, each account will produce about one tweet per day.
			</p>
		</a>

		<a href="atom.php" id="atom" class="format" title="ATOM Syndication">
			<h2>Atom Syndication</h2>
			<p>
				A basic syndication format supported by a variety of feed readers.
				This is a good option for casually subscribing to earthquake
				information.
			</p>
		</a>

		<a href="csv.php" id="csv" class="format"
				title="Spreadsheet Applications">
			<h2>Spreadsheet Applications</h2>
			<p>
				A simple text format suitable for loading data into spreadsheet
				applications like Microsoft Excel&trade;. This is a good option for
				manual scientific analysis.
			</p>
		</a>

		<a href="quakeml.php" id="quakeml" class="format"
				title="QuakeML">
			<h2>QuakeML</h2>
			<p>
				A flexible, extensible and modular XML representation of 
				seismological data which is intended to cover a broad range of 
				fields of application in modern seismology.
			</p>
		</a>
	</div>

	<div class="one-of-two column">
		<a href="https://sslearthquake.usgs.gov/ens/" id="ens" class="format"
				title="Earthquake Notification Service">
			<h2>Earthquake Notification Service</h2>
			<p>
				The Earthquake Notification Service (ENS) is a free service that
				sends you automated notifications to your email or cell phone when
				earthquakes happen.
			</p>
		</a>

		<a href="kml.php" id="kml" class="format"
				title="Google Earth KML">
			<h2>Google Earth KML</h2>
			<p>
				This feed format is suitable for loading into applications that
				understand Keyhole Markup Language (KML) such as Google
				Earth&trade;.
			</p>
		</a>

		<a href="geojson.php" id="api" class="format"
				title="Programmatic Access">
			<h2>Programmatic Access</h2>
			<p>
				A well-structured format readily parsed by most programming
				languages. This is a good option for software developers
				wishing to use earthquake data.
			</p>
		</a>
	</div>
</div>
