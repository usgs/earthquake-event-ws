<?php
	date_default_timezone_set('UTC');
	$now = time();
	$starttime = $now - 604800; // 1 week ago
	$starttime -= ($starttime % 86400); // Round to start of day
	$starttime = date('Y-m-d H:i:s', $starttime);

	$endtime = $now + 86400; // Tomorrow
	$endtime -= ($endtime % 86400); // Round to start of day (i.e. end of today)
	$endtime = date('Y-m-d H:i:s', $endtime - 1);
?>

<div class="one-of-two column">
	<section aria-labelledby="datetime">
		<h3 id="datetime" class="label">Date &amp; Time</h3>
		<ul class="vertical no-style">
			<li>
				<label for="starttime" class="label" id="datetime-starttime">
					Start (UTC)
				</label>
				<input type="text" name="starttime" id="starttime"
						placeholder="yyyy-mm-dd hh:mm:ss"
						value="<?php print $starttime; ?>"
						aria-labelledby="datetime datetime-starttime"/>
			</li><li>
				<label for="endtime" class="label" id="datetime-endtime">
					End (UTC)
				</label>
				<input type="text" name="endtime" id="endtime"
						placeholder="yyyy-mm-dd hh:mm:ss"
						value="<?php print $endtime; ?>"
						aria-labelledby="datetime datetime-endtime"/>
			</li>
		</ul>
	</section>

	<section aria-labelledby="magnitude">
		<h3 id="magnitude" class="label">Magnitude</h3>
		<ul class="vertical no-style two-up">
			<li>
				<label for="minmagnitude" class="label" id="magnitude-minmagnitude">
					Minimum
				</label>
				<input type="number" step="any" name="minmagnitude" id="minmagnitude"
						min="-1" max="10" step="0.1" value="6.0"
						aria-labelledby="magnitude magnitude-minmagnitude"/>
			</li><li>
				<label for="maxmagnitude" class="label" id="magnitude-maxmagnitude">
					Maximum
				</label>
				<input type="number" step="any" name="maxmagnitude" id="maxmagnitude"
						min="-1" max="10" step="0.1"
						aria-labelledby="magnitude magnitude-maxmagnitude"/>
			</li>
		</ul>
	</section>

	<section aria-labelledby="depth">
		<h3 id="depth" class="label">Depth (km)</h3>
		<ul class="vertical no-style two-up">
			<li>
				<label for="mindepth" class="label" id="depth-mindepth">Minimum</label>
				<input type="number" step="any" name="mindepth" id="mindepth"
						min="-100" max="1000" step="0.1"
						aria-labelledby="depth depth-mindepth"/>
			</li><li>
				<label for="maxdepth" class="label" id="depth-maxdepth">Maximum</label>
				<input type="number" step="any" name="maxdepth" id="maxdepth"
						min="-100" max="1000" step="0.1"
						aria-labelledby="depth depth-maxdepth"/>
			</li>
		</ul>
	</section>
</div>

<div class="one-of-two column">
	<section aria-labelledby="region">
		<h3 id="region" class="label">Geographic Region</h3>
		<div class="region-description"></div>

		<div class="fieldset" role="group" aria-labelledby="region-rectangle">
			<h4 id="region-rectangle" class="label">Rectangle</h4>
			<p class="help">
				Decimal degree coordinates. North must be greater than South. East
				must be greater than West.
			</p>

			<button type="button" class="draw orange">Draw Rectangle on Map</button>

			<ul class="region-square-details no-style vertical">
			<li class="region-square-details-north">
				<label for="maxlatitude" class="label" id="rectangle-maxlatitude">
					North
				</label>
				<input type="number" step="any" name="maxlatitude" id="maxlatitude"
						min="-90.0" max="90"
						aria-labelledby="region region-rectangle rectangle-maxlatitude"/>
			</li>
			<li class="region-square-details-west">
				<label for="minlongitude" class="label" id="rectangle-minlongitude">
					West
				</label>
				<input type="number" step="any" name="minlongitude" id="minlongitude"
						min="-360" max="360"
						aria-labelledby="region region-rectangle rectangle-minlongitude"/>
			</li>
			<li class="region-square-details-east">
				<label for="maxlongitude" class="label" id="rectangle-maxlongitude">
					East
				</label>
				<input type="number" step="any" name="maxlongitude" id="maxlongitude"
						min="-360" max="360"
						aria-labelledby="region region-rectangle rectangle-maxlongitude"/>
			</li>
			<li class="region-square-details-south" id="rectangle-minlatitude">
				<label for="minlatitude" class="label">South</label>
				<input type="number" step="any" name="minlatitude" id="minlatitude"
						min="-90" max="90"
						aria-labelledby="region region-rectangle rectangle-maxlatitude"/>
			</li>
			</ul>
		</div>

		<div class="fieldset" role="group" aria-labelledby="region-circle">
			<h4 id="region-circle" class="label">
				Circle/ Donut
			</h4>
			<p class="help">
				Specify an inner radius to perform a donut search.
			</p>

			<ul class="vertical no-style two-up">
				<li>
					<label for="latitude" class="label" id="circle-latitude">
						Center Latitude
					</label>
					<input type="number" step="any" name="latitude" id="latitude"
							min="-90" max="90"
							aria-labelledby="region region-circle circle-latitude"/>
				</li>
				<li>
					<label for="longitude" class="label" id="circle-longitude">
						Center Longitude
					</label>
					<input type="number" step="any" name="longitude" id="longitude"
							min="-180" max="180"
							aria-labelledby="region region-circle circle-longitude"/>
				</li>
				<li>
					<label for="minradiuskm" class="label" id="circle-minradiuskm">
						<abbr title="Minimum">Inner</abbr> Radius (km) Optional
					</label>
					<input type="number" step="any" name="minradiuskm" id="minradiuskm"
							min="0" max="20001.6"
							aria-labelledby="region region-circle circle-minradiuskm"/>
				</li>
				<li>
					<label for="maxradiuskm" class="label" id="circle-maxradiuskm">
						<abbr title="Maximum">Outer</abbr> Radius (km)
					</label>
					<input type="number" step="any" name="maxradiuskm" id="maxradiuskm"
							min="0" max="20001.6"
							aria-labelledby="region region-circle circle-maxradiuskm"/>
				</li>
			</ul>
		</div>
	</section>
</div>
