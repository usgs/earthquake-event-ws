<?php
	include_once('../conf/feed.inc.php');
	$TITLE = 'Web Service URL Builder';
	if (preg_match('/comcat/', $_SERVER['HTTP_HOST'])) {
		$TITLE .= ' BETA';
	}
	$STYLESHEETS = 'css/urlbuilder.css';
	$SCRIPTS = 'js/urlbuilder.js';
	include_once $_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php';

	if (!function_exists("helpurl")) {
		function helpurl($inputname) {
			global $FDSN_HOST;
			global $FDSN_PATH;

			return '<small>' .
					'<a target="apidocs"' .
						' href="' . $FDSN_HOST . $FDSN_PATH . '#' . $inputname . '"' .
						'>' .
					'help for ' . $inputname . 
					'</a>' .
					'</small>';
		}
	}
?>

<noscript>
This page provides a URL builder for the Web Service API that requires javascript to work.
</noscript>
<p>
You can also read the <a href="<?php echo $FDSN_HOST . $FDSN_PATH; ?>">Web Service API Documentation</a> and manually build URLs for the Event web service.
</p>

<form action="<?php echo $FDSN_HOST . $FDSN_PATH; ?>/query" method="GET" id="search_form" class="nojs">
<div class="ten column">
<p>All fields are optional.  Help links open in a new window.</p>


<h2>Event ID</h2>
	<p class="help">All other event options are ignored when searching by Event ID</p>
	<ul>
	<li>
		<label for="eventid">Event Id</label>
		<?php echo helpurl('eventid'); ?>
		<input type="text" name="eventid" id="eventid"/>
	</li>
	</ul>


<h2>Time</h2>
	<p class="help">All Times use ISO8601 Format, for example: <?php echo gmdate("c"); ?></p>

	<ul>
	<li class="min">
		<label for="starttime">Start Time <?php echo helpurl('starttime'); ?></label>
		<input type="datetime" name="starttime" id="starttime"/>
	</li>
	<li class="max">
		<label for="endtime">End Time <?php echo helpurl('endtime'); ?></label>
		<input type="datetime" name="endtime" id="endtime"/>
	</li>
	<li>
		<label for="updatedafter">Updated After <?php echo helpurl('updatedafter'); ?></label>
		<input type="datetime" name="updatedafter" id="updatedafter" />
	</li>
	</ul>


<h2>Location</h2>

	<fieldset>
		<legend>Rectangle</legend>
		<ul>
		<li class="min">
			<label for="minlatitude">Minimum Latitude <?php echo helpurl('minlatitude'); ?></label>
			<input type="number" name="minlatitude" id="minlatitude" min="-90" max="90" step="0.001" placeholder="-90"/>
		</li>
		<li class="max">
			<label for="maxlatitude">Maximum Latitude <?php echo helpurl('maxlatitude'); ?></label>
			<input type="number" name="maxlatitude" id="maxlatitude" min="-90" max="90" step="0.001" placeholder="90"/>
		</li>
		<li class="min">
			<label for="minlongitude">Minimum Longitude <?php echo helpurl('minlongitude'); ?></label>
			<input type="number" name="minlongitude" id="minlongitude" min="-360" max="360" step="0.001" placeholder="-180"/>
		</li>
		<li class="max">
			<label for="maxlongitude">Maximum Longitude <?php echo helpurl('maxlongitude'); ?></label>
			<input type="number" name="maxlongitude" id="maxlongitude" min="-360" max="360" step="0.001" placeholder="180"/>
		</li>
		</ul>
	</fieldset>

	<fieldset>
		<legend>Circle</legend>
		<p class="help">
			Circle searches require all of Latitude, Longitude and Maximum Radius.
			<br/>To convert kilometers to degrees, divide by 111.2.
		</p>
		<ul>
		<li>
			<label for="latitude">Latitude <?php echo helpurl('latitude'); ?></label>
			<input type="number" name="latitude" id="latitude" min="-90" max="90" step="0.001"/>
		</li>
		<li>
			<label for="longitude">Longitude <?php echo helpurl('longitude'); ?></label>
			<input type="number" name="longitude" id="longitude" min="-180" max="180" step="0.001"/>
		</li>
		<li class="min">
			<label for="minradius">Minimum Radius (degrees) <?php echo helpurl('minradius'); ?></label>
			<input type="number" name="minradius" id="minradius" min="0" max="180" step="0.001"/>
		</li>
		<li class="max">
			<label for="maxradius">Maximum Radius (degrees) <?php echo helpurl('maxradius'); ?></label>
			<input type="number" name="maxradius" id="maxradius" min="0" max="180" step="0.001"/>
		</li>
		</ul>
	</fieldset>


<h2>Magnitude</h2>
	<ul>
	<li class="min">
		<label for="minmagnitude">Minimum Magnitude <?php echo helpurl('minmagnitude'); ?></label>
		<input type="number" name="minmagnitude" id="minmagnitude" min="-1" max="10" step="0.1"/>
	</li>
	<li class="max">
		<label for="maxmagnitude">Maximum Magnitude <?php echo helpurl('maxmagnitude'); ?></label>
		<input type="number" name="maxmagnitude" id="maxmagnitude" min="-1" max="10" step="0.1"/>
	</li>
	<li>
		<label for="magnitudetype">Magnitude Type <?php echo helpurl('magnitudetype'); ?></label>
		<input type="text" name="magnitudetype" id="magnitudetype"/>
	</li>
	</ul>


<h2 id="advanced">Advanced</h2>
	<ul>
	<li class="min">
		<label for="mindepth">Minimum Depth (kilometers) <?php echo helpurl('mindepth'); ?></label>
		<input type="number" name="mindepth" id="mindepth" min="-100" max="1000" step="0.1"/>
	</li>
	<li class="max">
		<label for="maxdepth">Maximum Depth <?php echo helpurl('maxdepth'); ?></label>
		<input type="number" name="maxdepth" id="maxdepth" min="-100" max="1000" step="0.1"/>
	</li>

	<li>
		<label for="catalog">Catalog <?php echo helpurl('catalog'); ?></label>
		<select name="catalog" id="catalog">
			<option value=""></option>
			<option value="ak">AK - Alaska Earthquake Information Center</option>
			<option value="at">AT - Alaska Tsunami Warning Center</option>
			<option value="ci">CI - California Institute of Technology</option>
			<option value="hv">HV - Hawaiian Volcano Observatory</option>
			<option value="mb">MB - Montana Bureau of Mines and Geology</option>
			<option value="nc">NC - Northern California Seismic Network</option>
			<option value="nm">NM - St. Louis University</option>
			<option value="nn">NN - University of Nevada</option>
			<option value="pde">PDE - NEIC</option>
			<option value="pr">PR - Red Sismica de Puerto Rico</option>
			<option value="pt">PT - Pacific Tsunami Warning Center</option>
			<option value="se">SE - Virginia Tech</option>
			<option value="us">US - National Earthquake Information Center</option>
			<option value="uu">UU - University of Utah</option>
			<option value="uw">UW - University of Washington</option>
		</select>
	</li>
	<li>
		<label for="contributor">Contributor <?php echo helpurl('contributor'); ?></label>
		<select name="contributor" id="contributor">
			<option value=""></option>
			<option value="pde">PDE - NEIC</option>
			<option value="ak">AK - Alaska Earthquake Information Center</option>
			<option value="at">AT - Alaska Tsunami Warning Center</option>
			<option value="ci">CI - California Institute of Technology</option>
			<option value="hv">HV - Hawaiian Volcano Observatory</option>
			<option value="mb">MB - Montana Bureau of Mines and Geology</option>
			<option value="nc">NC - Northern California Seismic Network</option>
			<option value="nm">NM - St. Louis University</option>
			<option value="nn">NN - University of Nevada</option>
			<option value="pr">PR - Red Sismica de Puerto Rico</option>
			<option value="pt">PT - Pacific Tsunami Warning Center</option>
			<option value="se">SE - Virginia Tech</option>
			<option value="us">US - National Earthquake Information Center</option>
			<option value="uu">UU - University of Utah</option>
			<option value="uw">UW - University of Washington</option>
			<option value="atlas">ShakeMap Atlas</option>
			<option value="official">Official</option>
		</select>
	</li>
	<li class="extension">
		<label for="reviewstatus">Review Status <?php echo helpurl('reviewstatus'); ?></label>
		<select name="reviewstatus" id="reviewstatus">
			<option value=""></option>
			<option>automatic</option>
			<option>reviewed</option>
		</select>
	</li>
	<li class="extension">
		<label for="eventtype">Event Type <?php echo helpurl('eventtype'); ?></label>
		<select name="eventtype" id="eventtype">
			<option value=""></option>
			<option value="earthquake">Earthquake</option>
			<option value="induced earthquake">Induced Earthquake</option>
			<option value="quarry blast">Quarry Blast</option>
			<option value="quarry">Quarry</option>
			<option value="explosion">Explosion</option>
			<option value="chemical explosion">Chemical Explosion</option>
			<option value="nuclear explosion">Nuclear Explosion</option>
			<option value="nuke">Nuke</option>
			<option value="landslide">Landslide</option>
			<option value="rockslide">Rockslide</option>
			<option value="rockfall">Rockfall</option>
			<option value="rockburst">Rockburst</option>
			<option value="snow avalanche">Snow Avalanche</option>
			<option value="debris avalanche">Debris Avalanche</option>
			<option value="mine collapse">Mine Collapse</option>
			<option value="building collapse">Building Collapse</option>
			<option value="volcanic eruption">Volcanic Eruption</option>
			<option value="meteor impact">Meteor Impact</option>
			<option value="plane crash">Plane Crash</option>
			<option value="sonic boom">Sonic Boom</option>
			<option value="sonicboom">Sonicboom</option>
			<option value="not existing">Not Existing</option>
			<option value="shot">Shot</option>
			<option value="other">Other</option>
		</select>
	</li>
	<li class="extension">
		<label for="producttype">Product Type <?php echo helpurl('producttype'); ?></label>
		<select name="producttype" id="producttype">
			<option value=""></option>
			<option value="dyfi">DYFI?</option>
			<option value="shakemap">ShakeMap</option>
			<option value="losspager">PAGER</option>
			<option value="focal-mechanism">Focal Mechanism</option>
			<option value="moment-tensor">Moment Tensor</option>
			<option value="phase-data">Phase Data</option>
		</select>
	</li>

	<li class="extension">
		<label for="alertlevel">
			<abbr title="Prompt Assessment of Global Earthquakes for Response">PAGER</abbr>
			Alert Level <?php echo helpurl('alertlevel'); ?>
		</label>
		<select name="alertlevel" id="alertlevel">
			<option value=""></option>
			<option>green</option>
			<option>yellow</option>
			<option>orange</option>
			<option>red</option>
		</select>
	</li>

	<li class="min extension">
		<label for="minmmi">
			Minimum ShakeMap <abbr title="Modified Mercalli Intensity">MMI</abbr>
			<?php echo helpurl('minmmi'); ?>
		</label>
		<input type="number" name="minmmi" id="minmmi" min="0" max="12" step="0.1"/>
	</li>
	<li class="max extension">
		<label for="maxmmi">
			Maximum ShakeMap <abbr title="Modified Mercalli Intensity">MMI</abbr>
			<?php echo helpurl('maxmmi'); ?>
		</label>
		<input type="number" name="maxmmi" id="maxmmi" min="0" max="12" step="0.1"/>
	</li>

	<li class="min extension">
		<label for="mincdi">
			Minimum DYFI? <abbr title="Community Determined Intensity">CDI</abbr>
			<?php echo helpurl('mincdi'); ?>
		</label>
		<input type="number" name="mincdi" id="mincdi" min="0" max="12" step="0.1"/>
	</li>
	<li class="max extension">
		<label for="maxcdi">
			Maximum DYFI? <abbr title="Community Determined Intensity">CDI</abbr>
			<?php echo helpurl('maxcdi'); ?>
		</label>
		<input type="number" name="maxcdi" id="maxcdi" min="0" max="12" step="0.1"/>
	</li>
	<li class="extension">
		<label for="minfelt">
			Minimum number of DYFI? responses
			<?php echo helpurl('minfelt'); ?>
		</label>
		<input type="number" name="minfelt" id="minfelt" min="0"/>
	</li>

	<li class="min extension">
		<label for="minsig">Minimum Significance <?php echo helpurl('minsig'); ?></label>
		<input type="number" name="minsig" id="minsig" min="0"/>
	</li>
	<li class="max extension">
		<label for="maxsig">Maximum Significance <?php echo helpurl('maxsig'); ?></label>
		<input type="number" name="maxsig" id="maxsig" min="0"/>
	</li>

	<li class="min extension">
		<label for="mingap">Minimum Azimuthal Gap <?php echo helpurl('mingap'); ?></label>
		<input type="number" name="mingap" id="mingap" min="0" max="360" step="0.01"/>
	</li>
	<li class="max extension">
		<label for="maxgap">Maximum Azimuthal Gap <?php echo helpurl('maxgap'); ?></label>
		<input type="number" name="maxgap" id="maxgap" min="0" max="360" step="0.01"/>
	</li>
	</ul>


<h2>Output Options</h2>
	<ul>
	<li>
		<label for="orderby">Order By <?php echo helpurl('orderby'); ?></label>
		<select name="orderby" id="orderby">
			<option value="">Time (Newest First)</option>
			<option value="time-asc">Time (Oldest First)</option>
			<option value="magnitude">Magnitude (Largest First)</option>
			<option value="magnitude-asc">Magnitude (Smallest First)</option>
		</select>
	</li>
	<li class="min">
		<label for="limit">Limit <?php echo helpurl('limit'); ?></label>
		<input type="number" name="limit" id="limit" min="1" max="<?php echo $MAX_SEARCH; ?>"/>
	</li>
	<li class="max">
		<label for="offset">Offset <?php echo helpurl('offset'); ?></label>
		<input type="number" name="offset" id="offset" min="1"/>
	</li>

	<li class="extension">
		<label for="format">Output Format <?php echo helpurl('format'); ?></label>
		<select name="format" id="format">
			<option value="">Quakeml 1.2</option>
			<option value="csv">CSV</option>
			<option value="geojson">GeoJSON</option>
			<option value="kml">KML</option>
		</select>
	</li>

	<li class="quakeml-only">
		<label for="includeallorigins">
			<input type="checkbox" name="includeallorigins" id="includeallorigins" value="true"/>
			Include all origins
			<?php echo helpurl('includeallorigins'); ?>
		</label>
	</li>
	<li class="quakeml-only">
		<label for="includeallmagnitudes">
			<input type="checkbox" name="includeallmagnitudes" id="includeallmagnitudes" value="true"/>
			Include all magnitudes
			<?php echo helpurl('includeallmagnitudes'); ?>
		</label>
	</li>
	<li class="quakeml-only">
		<label for="includearrivals">
			<input type="checkbox" name="includearrivals" id="includearrivals" value="true" disabled="disabled"/>
			Include arrivals
			<?php echo helpurl('includearrivals'); ?>
		</label>
	</li>
	<li class="geojson-only extension">
		<label for="callback">Callback <?php echo helpurl('callback'); ?></label>
		<input type="text" name="callback" id="callback"/>
	</li>
	<li class="kml-only extension">
		<label for="kmlcolorby">KML Color By <?php echo helpurl('kmlcolorby'); ?></label>
		<select name="kmlcolorby" id="kmlcolorby">
			<option value="">age</option>
			<option>depth</option>
		</select>
	</li>
	<li class="kml-only extension">
		<label for="kmlanimated">
			<input type="checkbox" name="kmlanimated" id="kmlanimated" value="true"/>
			Animated KML <?php echo helpurl('kmlanimated'); ?>
		</label>
	</li>
	</ul>


</div>

<div class="ten column">
	<h2>Search URL</h2>
	<p class="help">Click the URL below to search</p>
	<p id="search_url">When javascript is enabled, a URL for the search will be updated as you fill out the form.</p>
</div>
</form>
