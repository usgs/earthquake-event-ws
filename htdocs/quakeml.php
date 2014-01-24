<?php
	$TITLE = 'QuakeML Summary Format';
	$STYLESHEETS = 'css/feedPages.css,css/format.css';
	include_once $_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';
	include_once 'inc/terms.inc.php';
	include_once('../conf/feed.inc.php');
	$format = 'quakeml';
?>


<div id="feed-format">

	<div class="column seven">
	
	<h3>Description</h3>
		<p>
			This documentation provides information on the details of the QuakeML source response. For more information please see the <a href="https://quake.ethz.ch/quakeml/">QuakeML website</a> for a better understanding of the specification. 
		</p>
		<p>
			This feed adheres to the USGS Earthquakes 
			<a href="/earthquakes/feed/v1.0/../policy.php">Feed Life Cycle Policy</a>.
		</p>
	
	
		<h3>Usage</h3>
	
		<p>
			To request this output format, use &ldquo;format=quakeml&rdquo;.
		</p>
		
		
		<h3>Output</h3>
		
		<p>
		For a description of the QuakeML format, view the <a href="https://quake.ethz.ch/quakeml/Documents" target="_blank">Documents</a> section of the QuakeML website.
		
		<a href="https://quake.ethz.ch/quakeml/Documents">
			<img class="screenshot" src="images/screenshot_quakeml.png" title="QuakeML UML class diagram" target="_blank" />
		</a>
		
		</p>

		
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
