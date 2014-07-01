<?php
if (!isset($TEMPLATE)) {
	include_once '../conf/config.inc.php';
	$TITLE = 'Feeds &amp; Notifications';
	$HEAD = '
		<link rel="stylesheet" href="css/index.css"/>
	';

	include 'template.inc.php';
}
?>

<div class="row">
	<div class="one-of-two column">
	<h2>Real-time Feeds</h2>

	<a href="atom.php" class="format atom-icon" title="ATOM Syndication">
		<h3>ATOM Syndication</h3>
		<p>
			A basic syndication format supported by a variety of feed readers.
			This is a good option for casually subscribing to earthquake
			information.
		</p>
	</a>

	<a href="kml.php" class="format kml-icon"
			title="Google Earth KML">
		<h3>Google Earth KML</h3>
		<p>
			This feed format is suitable for loading into applications that
			understand Keyhole Markup Language (KML) such as Google
			Earth&trade;.
		</p>
	</a>

	<a href="csv.php" class="format csv-icon"
			title="Spreadsheet Applications">
		<h3>Spreadsheet Applications</h3>
		<p>
			A simple text format suitable for loading data into spreadsheet
			applications like Microsoft Excel&trade;. This is a good option for
			manual scientific analysis.
		</p>
	</a>

	<a href="quakeml.php" class="format quakeml-icon"
			title="QuakeML">
		<h3>QuakeML</h3>
		<p>
			A flexible, extensible and modular XML representation of
			seismological data which is intended to cover a broad range of
			fields of application in modern seismology.
		</p>
	</a>

	</div>

	<div class="one-of-two column">
	<h2>Real-time Notifications</h2>

	<a href="https://sslearthquake.usgs.gov/ens/" class="format ens-icon"
			title="Earthquake Notification Service">
		<h3>Earthquake Notification Service</h3>
		<p>
			The Earthquake Notification Service (ENS) is a free service that
			sends you automated notifications to your email or cell phone when
			earthquakes happen.
		</p>
	</a>

	<a href="/earthquakes/ted/" class="format ted-icon"
			title="Tweet Earthquake Dispatch">
		<h3>Tweet Earthquake Dispatch</h3>
		<p>
			Tweet Earthquake Dispatch (TED) offers two Twitter accounts. On
			average, each account will produce about one tweet per day.
		</p>
	</a>

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


<!--

<div class="row">
	<h2>Programmatic Access</h2>

	<div class="one-of-two column">
		<a href="geojson.php" class="format geojson-icon"
				title="Geojson">
			<h2>GeoJSON</h2>
			<p>
				A well-structured format readily parsed by most programming
				languages. This is a good option for software developers
				wishing to use earthquake data.
			</p>
		</a>
	</div>
	<div class="one-of-two column">
		<a href="https://github.com/usgs/devcorner" class="format api-icon"
				title="The ANSS Developer's Corner">
			<h2>The ANSS Developer's Corner</h2>
			<p>
				Software tools to access information in real-time feeds and the
				comprehensive catalog.
			</p>
		</a>
	</div>
</div>

<div class="row">
	<h2>Mailing List</h2>
	<div class="one-of-two column">
		<a href="https://geohazards.usgs.gov/mailman/listinfo/realtime-feeds"
				class="format announce-icon" title="Real-time feeds email list">
			<h2>Announcements <small>(realtime-feeds)</small></h2>
			<p>
				Notification of changes to earthquake feeds and data.
			</p>
		</a>
	</div>
	<div class="one-of-two column">
		<a href="https://geohazards.usgs.gov/mailman/listinfo/realtime-feed-users"
				class="format question-icon" title="Real-time feed users">
			<h2>Questions <small>(realtime-feed-users)</small></h2>
			<p>
				Questions from users of earthquake feeds and data.
			</p>
		</a>
	</div>
</div>

-->