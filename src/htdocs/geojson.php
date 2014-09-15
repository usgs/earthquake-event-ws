<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'GeoJSON Summary Format';
	$NAVIGATION = true;
	$HEAD = '<link rel="stylesheet" href="css/feedPages.css"/>';

	include '../conf/config.inc.php';
	include 'template.inc.php';
}
?>

<div class="row feed-format">
	<div class="column three-of-five">
		<h3>Description</h3>
		<p>
			GeoJSON is a format for encoding a variety of geographic data structures.
			A GeoJSON object may represent a geometry, a feature, or a collection of
			features. GeoJSON uses the
			<a href="http://www.json.org/" target="_blank">JSON standard</a>.
			The GeoJSONP feed uses the same JSON response, but the GeoJSONP response
			is wrapped inside the function call, eqfeed_callback. See the
			<a href="http://www.geojson.org/" target="_blank">GeoJSON site</a>
			for more information.
		</p>
		<p>
			This feed adheres to the USGS Earthquakes
			<a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
		</p>

		<h3>Usage</h3>
		<p>
			GeoJSON is intended to be used as a programatic interface for
			applications.
		</p>

		<h3>Output</h3>
		<code class="geojson">{
	type: "FeatureCollection",
	metadata: {
		<a href="glossary.php#metadata_generated">generated</a>: Long Integer,
		<a href="glossary.php#metadata_url">url</a>: String,
		<a href="glossary.php#metadata_title">title</a>: String,
		<a href="glossary.php#metadata_api">api</a>: String,
		<a href="glossary.php#metadata_count">count</a>: Integer,
		<a href="glossary.php#metadata_status">status</a>: Integer
	},
	bbox: [
		<a href="glossary.php#longitude">minimum longitude</a>,
		<a href="glossary.php#latitude">minimum latitude</a>,
		<a href="glossary.php#depth">minimum depth</a>,
		<a href="glossary.php#longitude">maximum longitude</a>,
		<a href="glossary.php#latitude">maximum latitude</a>,
		<a href="glossary.php#depth">maximum depth</a>
	],
	features: [
		{
			type: "Feature",
			properties: {
				<a href="glossary.php#mag">mag</a>: Decimal,
				<a href="glossary.php#place">place</a>: String,
				<a href="glossary.php#time">time</a>: Long Integer,
				<a href="glossary.php#updated">updated</a>: Long Integer,
				<a href="glossary.php#tz">tz</a>: Integer,
				<a href="glossary.php#url">url</a>: String,
				<a href="glossary.php#detail">detail</a>: String,
				<a href="glossary.php#felt">felt</a>:Integer,
				<a href="glossary.php#cdi">cdi</a>: Decimal,
				<a href="glossary.php#mmi">mmi</a>: Decimal,
				<a href="glossary.php#alert">alert</a>: String,
				<a href="glossary.php#status">status</a>: String,
				<a href="glossary.php#tsunami">tsunami</a>: Integer,
				<a href="glossary.php#sig">sig</a>:Integer,
				<a href="glossary.php#net">net</a>: String,
				<a href="glossary.php#code">code</a>: String,
				<a href="glossary.php#ids">ids</a>: String,
				<a href="glossary.php#sources">sources</a>: String,
				<a href="glossary.php#types">types</a>: String,
				<a href="glossary.php#nst">nst</a>: Integer,
				<a href="glossary.php#dmin">dmin</a>: Decimal,
				<a href="glossary.php#rms">rms</a>: Decimal,
				<a href="glossary.php#gap">gap</a>: Decimal,
				<a href="glossary.php#magType">magType</a>: String,
				<a href="glossary.php#type">type</a>: String
			},
			geometry: {
				type: "Point",
				coordinates: [
					<a href="glossary.php#longitude">longitude</a>,
					<a href="glossary.php#latitude">latitude</a>,
					<a href="glossary.php#depth">depth</a>
				]
			},
			<a href="glossary.php#id">id</a>: String
		},
		&hellip;
	]
}</code>
	</div>

	<div class="column two-of-five">
		<h3>Feeds</h3>
		<?php
			$format = 'geojson';
			include_once 'inc/feedlinks.inc.php';
		?>
	</div>
</div>
