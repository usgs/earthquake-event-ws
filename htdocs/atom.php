<?php
	$TITLE = 'ATOM Format';

	$STYLESHEETS = 'css/feedPages.css,css/format.css';
	include_once('../conf/feed.inc.php');
	$format = 'atom';

	include_once $_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';
	include_once 'inc/terms.inc.php';
?>

<div id="feed-format">

	<div class="column seven">
	
	<h3>Description</h3>
		<p>
			This documentation goes over the details of the ATOM source response.
			Many browsers (or other feed readers) will render this format in a reader-specific manner.
			See the
			<a target="_blank" href="http://www.w3.org/2005/Atom">ATOM specification</a>
			or 
			<a target="_blank" href="http://www.atomenabled.org/developers/">Atom Enabled</a>
			for general information.
		</p>
		<p>
			This feed adheres to the USGS Earthquakes 
			<a href="/earthquakes/feed/v1.0/../policy.php">Feed Life Cycle Policy</a>.
		</p>
	
	
		<h3>Usage</h3>
	
		<p>
			To request this output format, use &ldquo;format=atom&rdquo;.
		</p>
		
		<h3>Output</h3>
		
		<p>Screenshot of the Magnitude Atom feed.</p>
		
		<img src="images/screenshot_atom.jpg" class="screenshot" alt="screenshot of the earthqauke Atom feed" />
		
	</div>

	<div class="column three">
		
		<h3>Feeds</h3>
		<h4>
			<?php print $dateRanges["hour"]["name"]; ?>
			<small>
				<?php print $dateRanges["hour"]["help"]; ?>
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
				<?php print $dateRanges["day"]["help"]; ?>
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
				<?php print $dateRanges["week"]["help"]; ?>
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
				<?php print $dateRanges["month"]["help"]; ?>
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
