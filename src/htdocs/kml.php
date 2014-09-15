<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'Google Earth&trade; KML';
	$NAVIGATION = true;
	$HEAD = '<link rel="stylesheet" href="css/feedPages.css"/>';

	include '../conf/config.inc.php';
	include 'inc/terms.inc.php';
	include 'template.inc.php';
}

$format = 'kml';
?>

<div class="row feed-format">

	<div class="column three-of-five">
		<h2>Description</h2>
		<p>
			KML is Google's
			<a href="https://developers.google.com/kml/" target="_blank">Keyhole
					Markup Language</a>. The KML feeds offer a variety of options, you
			can view earthquakes colored by age or depth, and an animated feed allows
			you to animate the series of earthquakes.
		</p>
		<p>
			This feed adheres to the USGS Earthquakes
			<a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
		</p>

		<h2>Usage</h2>
		<p>
			You will need to download and install <a href="http://earth.google.com/"
			target="_blank">Google Earth</a> to view KML files.
		</p>

		<dl class="kml-description">
			<dt>Automatic Feeds</dt>
			<dd>
				These feeds will automatically update every 5 or 15 minutes when
				downloaded and installed into Google Earth.
			</dd>

			<dt>Feeds</dt>
			<dd>
				Download a normal KML feed to view earthquake data in Google Earth. You
				will have to re-download the KML file to access updated earthquake
				information.
			</dd>
		</dl>

		<h2>Earthquake Animations</h2>
		<p>
			To view earthquake animations in Google Earth follow the directions below:
		</p>
		<ol>
			<li>Download an animated USGS Earthquakes KML Feed.</li>
			<li>Open the KML using Google Earth.</li>
			<li>Select the earthquake feed, in the left navigation.</li>
			<li>
				<a href="http://support.google.com/earth/bin/answer.py?hl=en&amp;answer=183758"
						target="_blank">Use the time slider</a>, in the upper left hand
				corner of the map, to animate the series of earthquakes. You may also
				click through the animation one frame at a time.
				<img class="screenshot" src="images/google-earth-time-slider.png"
						alt="screenshot of google earth time slider"/>
				<p>
					For more information on how to use the time slider,
					<a href="http://support.google.com/earth/bin/answer.py?hl=en&amp;answer=183758"
							target="_blank">click here</a>.
				</p>
			</li>
		</ol>

		<h2>Output</h2>
		<p>
			Screenshot of &ldquo;Past 7 Days, M1+ Earthquakes, Colored by Age&rdquo;
			KML in Google Earth.
		</p>
		<img src="images/screenshot_kml.jpg" class="screenshot"
				alt="screenshot of kml feed in google earth"/>
	</div>

	<div class="column two-of-five">
		<h2>Automatic Feeds</h2>

		<h3>
			<?php
				print $dateRanges['week']['name'] . ', ' . $magRanges['1']['name'];
			?>
		</h3>
			<small><?php print $dateRanges['week']['help']; ?></small>

		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_age_link.';
					print $format;
					print '">Colored by Age </a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_depth_link.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_age_animated_link.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_depth_animated_link.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>

		<h3>
			<?php
				print $dateRanges['month']['name'] . ', ' . $magRanges['2.5']['name'];
			?>
		</h3>
			<small><?php print $dateRanges['month']['help']; ?></small>
		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_age_link.';
					print $format;
					print '">Colored by Age</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_depth_link.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_age_animated_link.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_depth_animated_link.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>

		<h2>Feeds</h2>
		<h3>
			<?php
				print $dateRanges['week']['name'] . ', ' . $magRanges['1']['name'];
			?>
		</h3>
			<small><?php print $dateRanges['week']['help']; ?></small>
		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_age.';
					print $format;
					print '">Colored by Age</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_depth.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_age_animated.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['1']['url'];
					print "_";
					print $dateRanges['week']['url'];
					print '_depth_animated.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>

		<h3>
			<?php
				print $dateRanges['month']['name'] . ', ' . $magRanges['2.5']['name'];
			?>
		</h3>
			<small><?php print $dateRanges['month']['help']; ?></small>

		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_age.';
					print $format;
					print '">Colored by Age</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_depth.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_age_animated.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges['2.5']['url'];
					print "_";
					print $dateRanges['month']['url'];
					print '_depth_animated.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>
	</div>

</div>
