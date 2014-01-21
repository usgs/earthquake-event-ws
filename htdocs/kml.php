<?php
	$TITLE = 'KML Format';
	$STYLESHEETS = 'css/feedPages.css,css/format.css';
	include_once $_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';
	include_once 'inc/terms.inc.php';
	include_once('../conf/feed.inc.php');
	$format = 'kml';
?>

<div id="feed-format">

	<div class="column seven">
	
		<h3>Description</h3>
		
		<p>
			KML is Google's
			<a href="https://developers.google.com/kml/" target="_blank">Keyhole Markup Language</a>.
			The KML feeds offer a variety of options, you can view earthquakes colored by age or depth, and an animated feed allows you to animate the series of earthquakes.  
		</p>
		
		
		<h3>Usage</h3>
		
		<p>
			You will need to download and install <a href="http://earth.google.com/" 
			target="_blank">Google Earth</a> to view KML files. 
		</p>
		
		<dl class="kml-description"> 
			<dt>Feeds</dt>
			<dd>
				Download a normal KML feed to view earthquake data in Google Earth. You will have to re-download the KML file to access updated earthquake information. 
				
				
<!-- 				Your KML file will not update automatically if, but you can view the file in Google Earth without an internet connection.  -->
			</dd>
			
			<dt>Network Link Feeds</dt>
			<dd>
				Download a network link KML feed and the feed will update automatically, in Google Earth, with the most recent earthquake information every 5 or 15 minutes.
			</dd>
		</dl> 


	    <h3>Earthquake Animations</h3>
	    
	    <p>
	    	To view earthquake animations in Google Earth follow the directions below:
	    </p>
	    <ol>
	        <li>Download an animated USGS Earthquakes KML Feed.</li>
	        <li>Open the KML using Google Earth.</li>
	        <li>Select the earthquake feed, in the left navigation.</li>
	        <li><a href="http://support.google.com/earth/bin/answer.py?hl=en&amp;answer=183758" target="_blank">Use the time slider</a>, in the upper left hand corner of the map, to animate the series of earthquakes. You may also click through the animation one frame at a time.
	                <img class="screenshot" src="images/google-earth-time-slider.png" alt="screenshot of google earth time slider"/>
	
	                <p>
	                	For more information on how to use the time slider, 
	                	<a href="http://support.google.com/earth/bin/answer.py?hl=en&amp;answer=183758" target="_blank">click here</a>.
	                </p>
	        </li>
	    </ol>
	
	
		<h3>Output</h3>
		
		<p>
			Screenshot of "Past 7 Days, M1+ Earthquakes, Colored by Age" KML in Google Earth.
		</p>
		<img src="images/screenshot_kml.jpg" class="screenshot" alt="screenshot of kml feed in google earth"  />
	
	</div>
	
	<div class="column three">
	
		<h3>Feeds</h3>
		<h4>
			<?php
				print $dateRanges["week"]["name"];
				print ", ";
				print $magRanges["1"]["name"];
			?>
			<small>
				<?php print $dateRanges["week"]["help"]; ?> 
				
			</small>
		</h4>
	
		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_age.';
					print $format;
					print '">Colored by Age</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_depth.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_age_animated.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_depth_animated.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>
		
		<h4>
			<?php
				print $dateRanges["month"]["name"];
				print ", ";
				print $magRanges["2.5"]["name"];
			?>
			<small>
				<?php print $dateRanges["month"]["help"]; ?>
			</small>
		</h4>
		
		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_age.';
					print $format;
					print '">Colored by Age</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_depth.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_age_animated.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_depth_animated.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>
		
		
		<h3>Network Link Feeds</h3>
		
		<h4>
			<?php
				print $dateRanges["week"]["name"];
				print ", ";
				print $magRanges["1"]["name"];
			?>
			<small>
				<?php print $dateRanges["week"]["help"]; ?>
			</small>
		</h4>
		
	
		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_age_link.';
					print $format;
					print '">Colored by Age </a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_depth_link.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_age_animated_link.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["1"]["url"];
					print "_";
					print $dateRanges["week"]["url"];
					print '_depth_animated_link.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>
		
		<h4>
			<?php
				print $dateRanges["month"]["name"];
				print ", ";
				print $magRanges["2.5"]["name"];
			?>
			<small>
				<?php print $dateRanges["month"]["help"]; ?>
			</small>
		</h4>
		
		<ul>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_age_link.';
					print $format;
					print '">Colored by Age</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_depth_link.';
					print $format;
					print '">Colored by Depth</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_age_animated_link.';
					print $format;
					print '">Colored by Age, Animated</a>';
				?>
			</li>
			<li>
				<?php
					print '<a href="';
					print $SUMMARY_PATH;
					print $magRanges["2.5"]["url"];
					print "_";
					print $dateRanges["month"]["url"];
					print '_depth_animated_link.';
					print $format;
					print '">Colored by Depth, Animated</a>';
				?>
			</li>
		</ul>
		
	<!--
		<p>After downloaded, these KMLs will not update. </p>
		<p>These KMLs will update automatically in Google Earth.</p>
	-->
	
	</div>

</div>