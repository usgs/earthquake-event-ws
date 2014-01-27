<?php
	$TITLE = 'GeoJSON Summary Format';
	$STYLESHEETS = 'css/feedPages.css,css/format.css';
	include_once $_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';
	include_once 'inc/terms.inc.php';
	include_once('../conf/feed.inc.php');
	$format = 'geojson';
?>

<div id="feed-format">

	<div class="column seven">

		<h3>Description</h3>
		<p>
			GeoJSON is a format for encoding a variety of geographic data structures.
			A GeoJSON object may represent a geometry, a feature, or a collection of features.
			GeoJSON uses the
			<a href="http://www.json.org/" target="_blank">JSON standard</a>.
			The GeoJSONP feed uses the same JSON response, but the GeoJSONP response is wrapped inside the function call, eqfeed_callback.
			See the
			<a href="http://www.geojson.org/" target="_blank">GeoJSON site</a>
			for more information.
		</p>
                <p>
                        This feed adheres to the USGS Earthquakes
                        <a href="/earthquakes/feed/v1.0/../policy.php">Feed Life Cycle Policy</a>.
                </p>


		<h3>Usage</h3>

		<p>
			GeoJSON is intended to be used as a programatic interface for applications.
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
		...
	]
}</code>

	</div>

	<div class="column three">

		<h3>Feeds</h3>
		<h4>
			<?php print $dateRanges["hour"]["name"]; ?>
			<small>
				<?php print $geojson["hour"]["help"]; ?>
			</small>
		</h4>

		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["significant"]["url"];
					print "_";
					print $dateRanges["hour"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["significant"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["4.5"]["url"];
					print "_";
					print $dateRanges["hour"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["4.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["hour"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["2.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["hour"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["1"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["all"]["url"];
					print "_";
					print $dateRanges["hour"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["all"]["name"];
					print '</a>';
				?>
			</li>
		</ul>

		<h4>
			<?php print $dateRanges["day"]["name"]; ?>
			<small>
				<?php print $geojson["day"]["help"]; ?>
			</small>
		</h4>

		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["significant"]["url"];
					print "_";
					print $dateRanges["day"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["significant"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["4.5"]["url"];
					print "_";
					print $dateRanges["day"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["4.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["day"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["2.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["day"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["1"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["all"]["url"];
					print "_";
					print $dateRanges["day"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["all"]["name"];
					print '</a>';
				?>
			</li>
		</ul>

		<h4>
			<?php print $dateRanges["week"]["name"]; ?>
			<small>
				<?php print $geojson["week"]["help"]; ?>
			</small>
		</h4>

		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["significant"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["significant"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["4.5"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["4.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["2.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["1"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["all"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["all"]["name"];
					print '</a>';
				?>
			</li>
		</ul>

		<h4>
			<?php print $dateRanges["month"]["name"]; ?>
			<small>
				<?php print $geojson["month"]["help"]; ?>
			</small>
		</h4>

		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["significant"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["significant"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["4.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["4.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["2.5"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["1"]["name"];
					print '</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["all"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '.';
					print $format;
					print '">';
					print $magRanges["all"]["name"];
					print '</a>';
				?>
			</li>
		</ul>

	</div>
</div>
