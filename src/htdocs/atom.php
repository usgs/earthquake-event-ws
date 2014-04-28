<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'ATOM Format';
	$NAVIGATION = true;
	$HEAD = '<link rel="stylesheet" href="css/feedPages.css"/>';

	include '../conf/config.inc.php';
	include 'template.inc.php';
}
?>

<div id="feed-format" class="row">
	<div class="column seven-of-ten">
		<h3>Description</h3>
		<p>
			This documentation goes over the details of the ATOM source response.
			Many browsers (or other feed readers) will render this format in a
			reader-specific manner. See the
			<a target="_blank" href="http://www.w3.org/2005/Atom">
				ATOM specification
			</a>
			or
			<a target="_blank" href="http://www.atomenabled.org/developers/">
				Atom Enabled
			</a>
			for general information.
		</p>
		<p>
			This feed adheres to the USGS Earthquakes
			<a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
		</p>

		<h3>Usage</h3>
		<p>
			To request this output format, use &ldquo;format=atom&rdquo;.
		</p>

		<h3>Output</h3>
		<p>Screenshot of the Magnitude Atom feed.</p>
		<img src="images/screenshot_atom.jpg" class="screenshot"
				alt="screenshot of the earthqauke Atom feed"/>
	</div>

	<div class="column three-of-ten">
		<h3>Feeds</h3>
		<?php
			$format = 'atom';
			include_once 'inc/feedlinks.inc.php';
		?>
	</div>
</div>
