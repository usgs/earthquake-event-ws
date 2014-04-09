<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'QuakeML Summary Format';
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
			This documentation provides information on the details of the QuakeML
			source response. For more information please see the
			<a href="https://quake.ethz.ch/quakeml/">QuakeML website</a> for a better
			understanding of the specification. 
		</p>
		<p>
			This feed adheres to the USGS Earthquakes 
			<a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
		</p>

		<h3>Usage</h3>
		<p>
			To request this output format, use &ldquo;format=quakeml&rdquo;.
		</p>

		<h3>Output</h3>
		<p>
			For a description of the QuakeML format, view the
			<a href="https://quake.ethz.ch/quakeml/Documents" target="_blank">
					Documents</a> section of the QuakeML website.
			<a href="https://quake.ethz.ch/quakeml/Documents">
				<img class="screenshot" src="images/screenshot_quakeml.png"
						title="QuakeML UML class diagram" target="_blank" /></a>
		</p>
	</div>

	<div class="column three">
		<h3>Feeds</h3>
		<?php
			$format = 'quakeml';
			include_once 'inc/feedlinks.inc.php';
		?>
	</div>
</div>
