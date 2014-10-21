<?php

// entry point into the FDSN Event Webservice

if (!isset($TEMPLATE)) {
	$service = null;

	try {
		// configuration
		include_once '../conf/feeds.inc.php';

		// caching headers
		$CACHE_MAXAGE = 900;
		include_once '../lib/cache.inc.php';

		$service = new FDSNEventWebService($fdsnIndex);
		$usage = false;

		$method = param('method');
		if ($method === 'query') {
			$service->query();
		} else if ($method === 'catalogs') {
			$service->catalogs();
		} else if ($method === 'contributors') {
			$service->contributors();
		} else if ($method === 'count') {
		  $service->count();
		} else if ($method === 'version') {
			$service->version();
		} else if ($method === 'application.wadl') {
			$service->wadl();
		} else if ($method === 'application.json') {
			$service->application_json();
		} else {
			$usage = true;
		}

	} catch (Exception $e) {
		if ($service !== null) {
			$service->error(503, $e->getMessage());
		} else {
			header('HTTP/1.0 503 Service Unavailable');
			print_r($e);
		}
	}

	if (!$usage) {
		return;
	}


	$TITLE = 'API Documentation - Earthquake Catalog';
	$HEAD = '
	<style>
		dt, code {
			font-family: monospace;
			margin-top:1em;
		}
		dd {
			margin-left:40px;
		}
	</style>
	';
	$NAVIGATION = true;

	include 'template.inc.php';
}
?>

<p>
	This is an implementation of the FDSN Event Web Service Specification, and
	allows custom searches for earthquake information using a variety of
	parameters.  Please note that <strong>
	<a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/geojson.php">automated
	applications should use Real-time GeoJSON Feeds for displaying earthquake
	information whenever possible</a></strong>, as they will have the best
	performance and availability for that type of information.
</p>

<p>
	<a href="http://www.fdsn.org/webservices/FDSN-WS-Specifications-1.0.pdf">
	FDSN Web Service Specification PDF</a>.
</p>

<h2 id="url">URL</h2>
<p>
	<?php
		echo $HOST_URL_PREFIX . $FDSN_PATH;
	?>/[<a href="#methods">METHOD</a>[?<a href="#parameters">PARAMETERS</a>]]
</p>


<h2 id="methods">Methods</h2>
<dl>
	<dt>query</dt>
	<dd>
		to submit a data request.
	<p>
		<a href="#parameters">See the Parameters section for supported url
		parameters.</a>
	</p>
	</dd>

	<dt>catalogs</dt>
	<dd><a href="./catalogs">request available catalogs</a>.</dd>

	<dt>contributors</dt>
	<dd><a href="./contributors">request available contributors</a>.</dd>

	<dt>count</dt>
	<dd>
		to perform a count on a data request.
		<p>
			Count uses the same <a href="#parameters">parameters</a> as the query
			method, and is available in these <a href="#format">formats</a>: plain
			text (default), geojson, and xml.
		</p>
	</dd>

	<dt>version</dt>
	<dd><a href="./version">request full service version number</a>.</dd>

	<dt>application.wadl</dt>
	<dd>
		<a href="./application.wadl">request
		<abbr title="Web Application Description Language">WADL</abbr> for the
		interface</a>.
	</dd>

	<dt>application.json</dt>
	<dd>
		<a href="./application.json">request known enumerated parameter values for
		the interface</a>.
	</dd>
</dl>


<h2 id="parameters">Query method Parameters</h2>
<p>
	These parameters should be submitted as key=value pairs using the HTTP GET
	method and may not be specified more than once; if a parameter is submitted
	multiple times the result is undefined.
</p>

<h3>Time</h3>
<p>
	All times use ISO8601 Date/Time format. Unless a timezone is specified, UTC
	is assumed. Examples:
</p>
<dl>
	<dt><?php echo gmdate('Y-m-d'); ?></dt>
	<dd>Implicity UTC timezone, and time at start of the day (00:00:00)</dd>
	<dt><?php echo gmdate('Y-m-d\TH:i:s'); ?></dt>
	<dd>Implicit UTC timezone.</dd>
	<dt><?php echo gmdate('c'); ?></dt>
	<dd>Explicit timezone.</dd>
</dl>

<dl>
	<dt id="starttime">starttime</dt>
	<dd>
		ISO8601 Date/Time, e.g.
		<?php echo str_replace("+00:00", "", gmdate("c")); ?>
		<br/>
		<small>
			Default is NOW
			<?php echo ($DEFAULT_MAXEVENTAGE/86400); ?> days
		</small>
	</dd>
	<dd>Limit to events on or after the specified start time.</dd>

	<dt id="endtime">endtime</dt>
	<dd>
		ISO8601 Date/Time, e.g.
		<?php echo str_replace("+00:00", "", gmdate("c")); ?>
	</dd>
	<dd>Limit to events on or before the specified end time.</dd>

	<dt id="updatedafter">updatedafter</dt>
	<dd>
		ISO8601 Date/Time, e.g.
		<?php echo str_replace("+00:00", "", gmdate("c")); ?>
	</dd>
	<dd>Limit to events updated after the specified time.</dd>
</dl>

<h3>Location</h3>
<p>
	Requests that use both rectangle and circle will return the intersection,
	which may be empty, use with caution.
</p>

<h4>Rectangle</h4>
<p>
	Requests may use any combination of these parameters.
	<br/>
	<small>min values must be less than max values.</small>
	<br/>
	<small>
		rectangles may cross the date line by using a minlongitude&lt;-180 or
		maxlongitude&gt;180.
	</small>
</p>

<dl>
	<dt id="minlatitude">minlatitude</dt>
	<dd>Decimal degrees: [-90,90]</dd>
	<dd>Limit to events with a latitude larger than the specified minimum.</dd>

	<dt id="maxlatitude">maxlatitude</dt>
	<dd>Decimal degrees: [-90,90]</dd>
	<dd>Limit to events with a latitude smaller than the specified maximum.</dd>

	<dt id="minlongitude">minlongitude</dt>
	<dd>Decimal degrees: [-360,360]</dd>
	<dd>Limit to events with a longitude larger than the specified minimum.</dd>

	<dt id="maxlongitude">maxlongitude</dt>
	<dd>Decimal degrees: [-360,360]</dd>
	<dd>Limit to events with a longitude smaller than the specified maximum.</dd>
</dl>

<h4>Circle</h4>
<p>
	Requests must include all of latitude, longitude, and maxradius to perform a
	circle search.
</p>
<dl>
	<dt id="latitude">latitude</dt>
	<dd>Decimal degrees: [-90,90]</dd>
	<dd>Specify the latitude to be used for a radius search.</dd>

	<dt id="longitude">longitude</dt>
	<dd>Decimal degrees: [-180,180]</dd>
	<dd>Specify the longitude to be used for a radius search.</dd>

	<dt id="minradius">minradius</dt>
	<dd>Decimal degrees: [0,180].  Default 0.</dd>
	<dd>
		Limit to events further than the specified minimum number of degrees from
		the geographic point defined by the latitude and longitude parameters. This
		option is mutually exclusive with <a href="#minradiuskm">minradiuskm</a>
		and specifying both will result in an error.
	</dd>

	<dt id="minradiuskm">minradiuskm</dt>
	<dd>Kilometers: [0, 20001.6]</dd>
	<dd>
		Limit to events further than the specified minimum number of kilometers
		from the geographic point defined by the latitude and longitude parameters.
		This option is mutually exclusive with <a href="#minradius">minradius</a>
		and specifying both will result in an error.
	</dd>

	<dt id="maxradius">maxradius</dt>
	<dd>Decimal degrees: [0,180]</dd>
	<dd>
		Limit to events within the specified maximum number of degrees from the
		geographic point defined by the latitude and longitude parameters. This
		option is mutually exclusive with <a href="#maxradiuskm">maxradiuskm</a>
		and specifying both will result in an error.
	</dd>

	<dt id="maxradiuskm">maxradiuskm</dt>
	<dd>Kilometers: [0, 20001.6]</dd>
	<dd>
		Limit to events within the specified maximum number of kilometers from the
		geographic point defined by the latitude and longitude parameters. This
		option is mutually exclusive with <a href="#maxradius">maxradius</a> and
		specifying both will result in an error.
	</dd>
</dl>


<h3>Other</h3>
<dl>
	<dt id="mindepth">mindepth</dt>
	<dd>Decimal kilometers: [-100, 1000]</dd>
	<dd>Limit to events with depth more than the specified minimum.</dd>

	<dt id="maxdepth">maxdepth</dt>
	<dd>Decimal kilometers: [-100, 1000]</dd>
	<dd>Limit to events with depth less than the specified maximum.</dd>

	<dt id="minmagnitude">minmagnitude</dt>
	<dd>Decimal.</dd>
	<dd>Limit to events with a magnitude larger than the specified minimum.</dd>

	<dt id="maxmagnitude">maxmagnitude</dt>
	<dd>Decimal.</dd>
	<dd>Limit to events with a magnitude smaller than the specified maximum.</dd>

	<!-- TODO :: Conform magnitude type to FDSN spec
	<dt id="magnitudetype">magnitudetype</dt>
	<dd>
		Specify a magnitude type to use for testing the minimum and maximum limits.
		<br/>
		<small>
			NOTE: this will only return events reported with this magnitude
			type.
		</small>
		<p>
			Examples:&ldquo;Md&rdquo;, &ldquo;Ml&rdquo;, &ldquo;Ms&rdquo;,
			&ldquo;Mw&rdquo;, &ldquo;Me&rdquo;, &ldquo;Mi&rdquo;,
			&ldquo;Mb&rdquo;, &ldquo;MLg&rdquo;
		</p>
	</dd>
	-->

	<dt id="includeallorigins">includeallorigins</dt>
	<dd>Boolean "true"/"false".  Default false.</dd>
	<dd>
		Specify if all origins for the event should be included, default is data
		center dependent but is suggested to be the preferred origin only.
		<br/>
		<small>
			NOTE: because magnitudes and origins are strongly associated,
			this parameter is interchangable with includeallmagnitudes
		</small>
	</dd>

	<dt id="includeallmagnitudes">includeallmagnitudes</dt>
	<dd>Boolean "true"/"false".  Default false.</dd>
	<dd>
		Specify if all magnitudes for the event should be included, default is data
		center dependent but is suggested to be the preferred magnitude only.
		<br/>
		<small>
			NOTE: because magnitudes and origins are strongly associated, this
			parameter is interchangable with includeallmagnitudes
		</small>
	</dd>

	<dt id="includearrivals">includearrivals</dt>
	<dd>Boolean "true"/"false".  Default false.</dd>
	<dd>Specify if phase arrivals should be included.
		<br/><small>NOTE: NOT CURRENTLY IMPLEMENTED</small>
	</dd>

	<dt id="includedeleted">includedeleted</dt>
	<dd>Boolean &ldquo;true&rdquo;/&ldquo;false&rdquo;. Default false.</dd>
	<dd>Specify if deleted products should be incuded.
		<br/><small>
			NOTE: Only works when specifying <a href="#eventid">eventid</a>
			parameter.
		</small>
	</dd>

	<dt id="includesuperseded">includesuperseded</dt>
	<dd>Boolean &ldquo;true&rdquo;/&ldquo;false&rdquo;. Default false.</dd>
	<dd>Specify if superseded products should be included.
		This also includes all deleted products, and is mutually exclusive to
		the <a href="#includedeleted">includedeleted</a> parameter.
		<br/><small>
			NOTE: Only works when specifying <a href="#eventid">eventid</a>
			parameter.
		</small>
	</dd>

	<dt id="eventid">eventid</dt>
	<dd>Select a specific event by ID; event identifiers are data center specific.
		<br/>
		<small>
			NOTE: Selecting a specific event implies includeallorigins,
			includeallmagnitudes, and, additionally, associated moment tensor and
			focal-mechanisms are included.
		</small>
	</dd>

	<dt id="limit">limit</dt>
	<dd>
		Integer: [1,<?php echo $service->serviceLimit; ?>]. Default unlimited.
	</dd>
	<dd>
		Limit the results to the specified number of events.
		<br/>
		<small>
			NOTE: The service limits queries to <?php echo $service->serviceLimit; ?>,
			and any that exceed this limit will generate a HTTP response code
			&ldquo;400 Bad Request&rdquo;.
		</small>
	</dd>

	<dt id="offset">offset</dt>
	<dd>Integer: [1,&infin;]</dd>
	<dd>Return results starting at the event count specified, starting at 1.</dd>

	<dt id="orderby">orderby</dt>
	<dd>Order the result.
		<dl>
			<dt>time</dt>
			<dd>(Default) order by origin descending time</dd>
			<dt>time-asc</dt>
			<dd>order by origin ascending time</dd>
			<dt>magnitude</dt>
			<dd>order by descending magnitude</dd>
			<dt>magnitude-asc</dt>
			<dd>order by ascending magnitude</dd>
		</dl>
	</dd>

	<dt id="catalog">catalog</dt>
	<dd>
		Use the <a href="./catalogs">Catalogs Method</a> to find available catalogs.
	</dd>
	<dd>
		Limit to events from a specified catalog.
		<br/>
		<small>
			NOTE: when catalog and contributor are omitted, the most preferred
			information from any catalog or contributor for the event is returned.
		</small>
	</dd>

	<dt id="contributor">contributor</dt>
	<dd>
		Use the <a href="./contributors">Contributors Method</a> to find available
		contributors.
	</dd>
	<dd>
		Limit to events contributed by a specified contributor.
		<br/>
		<small>
			NOTE: when catalog and contributor are omitted, the most preferred
			information from any catalog or contributor for the event is returned.
		</small>
	</dd>
</dl>


<h3 id="extensions">Extensions</h3>
<dl>
	<dt id="format">format</dt>
	<dd>Specify the output format
		<dl>
			<dt>xml</dt>
			<dd>
				(Default) Response format is <a href="http://www.quakeml.org/">
				Quakeml 1.2</a>.  Mime-type is "application/xml".
			</dd>

			<dt>quakeml</dt>
			<dd>
				Alias for "xml" format.
			</dd>

			<dt>csv</dt>
			<dd>
				Response format is
				<a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/csv.php">CSV</a>.
				Mime-type is &ldquo;text/csv&rdquo;.
				<br/>
				<small>
					NOTE: only summary event information is available in this format.
				</small>
			</dd>

			<dt>geojson</dt>
			<dd>
				Response format is
				<a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/geojson.php">
				GeoJSON</a>. Mime-type is &ldquo;application/json&rdquo;.
				<dl>
					<dt id="callback">callback</dt>
					<dd>Convert GeoJSON output to a JSONP response using this callback.
						Mime-type is &ldquo;text/javascript&rdquo;.
					</dd>
					<dt id="jsonerror">jsonerror</dt>
					<dd>
						Request JSON(P) formatted output even on API error results.
						Accepts &ldquo;true&rdquo; or &ldquo;false&rdquo;. Default:
						&ldquo;false&rdquo;
					</dd>
				</dl>
			</dd>

			<dt>kml</dt>
			<dd>
				Response format is
				<a href="<?php echo $FEED_PATH . $FEED_PATH; ?>/kml.php">KML</a>.
				Mime-type is &ldquo;vnd.google-earth.kml+xml&rdquo;.
				<dl>
					<dt id="kmlcolorby">kmlcolorby</dt>
					<dd>
						How earthquakes are colored.
						"age" (default), or "depth".
					</dd>
					<dt id="kmlanimated">kmlanimated</dt>
					<dd>
						Whether to include timestamp in generated kml, for google earth
						animation support. &ldquo;false&rdquo; (default), or
						&ldquo;true&rdquo;.
					</dd>
				</dl>
			</dd>

			<dt>xml</dt>
			<dd>
				This format is only available for the count method. Response format is
				xml. Mime-type is &ldquo;application/xml&rdquo;.
			</dd>

			<dt>text</dt>
			<dd>
				This format is only available for the count and version methods.
				Response format is plain text. Mime-type is &ldquo;text/plain&rdquo;.
			</dd>
		</dl>
	</dd>

	<dt id="eventtype">eventtype</dt>
	<dd>
		Limit to events of a specific type.
		&ldquo;earthquake&ldquo; will filter non-earthquake events.
	</dd>

	<dt id="reviewstatus">reviewstatus</dt>
	<dd>
		Limit to events with a specific review status, default all.
		<dl>
			<dt>automatic</dt>
			<dd>Limit to events with review status "automatic".</dd>
			<dt>reviewed</dt>
			<dd>Limit to events with review status "reviewed".</dd>
		</dl>
	</dd>

	<dt id="minmmi">minmmi</dt>
	<dd>Decimal: [0,12]</dd>
	<dd>
		Minimum value for Maximum Modified Mercalli Intensity reported by ShakeMap.
	</dd>
	<dt id="maxmmi">maxmmi</dt>
	<dd>Decimal: [0,12]</dd>
	<dd>
		Maximum value for Maximum Modified Mercalli Intensity reported by ShakeMap.
	</dd>

	<dt id="mincdi">mincdi</dt>
	<dd>Decimal: [0,12]</dd>
	<dd>
		Minimum value for Maximum Community Determined Intensity reported by DYFI.
	</dd>
	<dt id="maxcdi">maxcdi</dt>
	<dd>Decimal: [0,12]</dd>
	<dd>
		Maximum value for Maximum Community Determined Intensity reported by DYFI.
	</dd>
	<dt id="minfelt">minfelt</dt>
	<dd>Positive Integer</dd>
	<dd>Limit to events with this many DYFI responses.</dd>

	<dt id="alertlevel">alertlevel</dt>
	<dd>
		Limit to events with a specific PAGER alert level, default all.
		<dl>
			<dt>green</dt>
			<dd>Limit to events with PAGER alert level "green".</dd>
			<dt>yellow</dt>
			<dd>Limit to events with PAGER alert level "yellow".</dd>
			<dt>orange</dt>
			<dd>Limit to events with PAGER alert level "orange".</dd>
			<dt>red</dt>
			<dd>Limit to events with PAGER alert level "red".</dd>
		</dl>
	</dd>

	<dt id="mingap">mingap</dt>
	<dd>Decimal degrees [0,360]</dd>
	<dd>Limit to events with no less than this azimuthal gap.</dd>

	<dt id="maxgap">maxgap</dt>
	<dd>Decimal degrees [0,360]</dd>
	<dd>Limit to events with no more than this azimuthal gap.</dd>

	<dt id="minsig">minsig</dt>
	<dd>Positive integer.</dd>
	<dd>Limit to events with no less than this significance.</dd>

	<dt id="maxsig">maxsig</dt>
	<dd>Positive integer.</dd>
	<dd>Limit to events with no more than this significance.</dd>

	<dt id="producttype">producttype</dt>
	<dd>
		Examples: &ldquo;moment-tensor&rdquo;, &ldquo;focal-mechanism&rdquo;,
		&ldquo;shakemap&rdquo;, &ldquo;losspager&rdquo;, &ldquo;dyfi&rdquo;.
	</dd>
	<dd>Limit to events that have this type of product associated.</dd>

	<dt id="nodata">nodata</dt>
	<dd>Integer: (204|404). Default 204.</dd>
	<dd>
		Define the error code that will be returned when no data is found.
	</dd>
</dl>
