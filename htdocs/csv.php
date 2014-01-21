<?php
	$TITLE = 'Spreadsheet Applications';
	$STYLESHEETS = 'css/feedPages.css,css/format.css';
	include_once('../conf/feed.inc.php');

	include_once $_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';
	include_once 'inc/terms.inc.php';

	$format = 'csv';

?>

<div id="feed-format">

	<div class="column seven">

		<h3>Description</h3>

		<p>
			CSV is a "comma separated values" ASCII text file.
			See the <a
            href="http://en.wikipedia.org/wiki/Comma-separated_values"
			target="_blank">wikipedia</a> for more information on this format.
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
		</ul>

		<p>Screenshot of the spreadsheet format, loaded into Microsoft Excel&trade;.</p>

		<img src="images/screenshot_csv.jpg" class="screenshot" alt="screenshot of kml feed in google earth" />

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
