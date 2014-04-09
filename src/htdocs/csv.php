<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'Spreadsheet Applications';
	$NAVIGATION = true;
	$HEAD = '
		<link rel="stylesheet" href="css/feedPages.css"/>
		<link rel="stylesheet" href="css/format.css"/>
	';

	include '../conf/config.inc.php';
	include 'template.inc.php';
}
?>

<div id="feed-format" class="row">
	<div class="column seven-of-ten">
		<h3>Description</h3>
		<p>
			CSV is a &ldquo;comma separated values&rdquo; ASCII text file.
			See the <a
					href="http://en.wikipedia.org/wiki/Comma-separated_values"
					target="_blank">wikipedia</a> for more information on this format.
		</p>
		<p>
			This feed adheres to the USGS Earthquakes
			<a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
		</p>

		<h3>Usage</h3>
		<p>
			A simple text format suitable for loading data into spreadsheet
			applications like Microsoft Excel&trade;. This is a good option for
			manual scientific analysis.
		</p>

		<h3>Output</h3>
		<p>
			Below are the fields included in the spreadsheet output:
		</p>
		<ul>
			<li><a href="glossary.php#time">time</a></li>
			<li><a href="glossary.php#latitude">latitude</a></li>
			<li><a href="glossary.php#longitude">longitude</a></li>
			<li><a href="glossary.php#depth">depth</a></li>
			<li><a href="glossary.php#mag">mag</a></li>
			<li><a href="glossary.php#magType">magType</a></li>
			<li><a href="glossary.php#nst">nst</a></li>
			<li><a href="glossary.php#gap">gap</a></li>
			<li><a href="glossary.php#dmin">dmin</a></li>
			<li><a href="glossary.php#rms">rms</a></li>
			<li><a href="glossary.php#net">net</a></li>
			<li><a href="glossary.php#id">id</a></li>
			<li><a href="glossary.php#updated">updated</a></li>
			<li><a href="glossary.php#place">place</a></li>
		</ul>

		<p>
			Screenshot of the spreadsheet format, loaded into Microsoft Excel&trade;.
		</p>
		<img src="images/screenshot_csv.jpg" class="screenshot"
				alt="screenshot of kml feed in google earth" />
	</div>

	<div class="column three-of-ten">
		<h3>Feeds</h3>
		<?php
			$format = 'csv';
			include_once 'inc/feedlinks.inc.php';
		?>
	</div>

</div>
