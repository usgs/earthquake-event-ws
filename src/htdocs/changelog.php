<?php

if (!isset($TEMPLATE)) {
	$TITLE = "Change Log";
	$NAVIGATION = true;

	include_once '../conf/config.inc.php';
	$FDSN_URL = $FDSN_HOST . $FDSN_PATH;

	include 'template.inc.php';
}
?>

<h2>v1.0.17 <small>2014-12-16</small></h2>
<ul>
	<li>Added plain text feed format.</li>
	<li>Reorganized search page layout.</li>
	<li>Fixed bug where un-reported values were displayed as 0.0.</li>
	<li>Set default event type to be &ldquo;earthquake&rdquo;.</li>
	<li>Support XML extension as valid feed format.</li>
	<li>Added nodata parameter to FDSN web service.</li>
	<li>Added option for &ldquo;includesuperceded&rsquo;.</li>
	<li>Restricted feed requests to HTTP GET method.</li>
	<li>Fixed event-time property for moment tensors.</li>
	<li>Added support for searching rectangle regions on a map.</li>
</ul>

<h2>v1.0.16 <small>2014-06-11</small></h2>
<ul>
	<li>Added access control headers for product contents.</li>
	<li>Increased marker size for small earthquakes in KML.</li>
	<li>Fixed network link bug in KML.</li>
	<li>Add kmlraw support to web service.</li>
	<li>Fixed string based time comparison when determining preferred order.</li>
	<li>Updated product ordering and enabled content in event KML.</li>
	<li>Updated ShakeMap information in event KML.</li>
	<li>Updated DYFI information in event KML.</li>
	<li>Added DYFI legend to event KML.</li>
</ul>

<h2>v1.0.15 <small>2014-06-02</small></h2>
<ul>
	<li>Fixed bug in KML format.</li>
	<li>Increased the significance value of a pager yellow alert to 650.</li>
	<li>
		Added programmatic access and mailing list sections to the
		summary page.
	</li>
	<li>Fixed Tsunami warning bug.</li>
	<li>Removed unused files, and cleaned up existing code.</li>
</ul>

<h2>v1.0.14 <small>2014-05-13</small></h2>
<ul>
	<li>Fixed various typos and normalized naming conventions.</li>
	<li>Cleaned up installation process.</li>
	<li>Migrated source code for offline development.</li>
	<li>Fixed bug in QuakeML output related to publicID.</li>
	<li>Updated maxradiuskm to allow for larger circular searches.</li>
	<li>Excludes deleted products from results.</li>
</ul>

<h2>v1.0.13 <small>2013-11-07</small></h2>
<ul>
	<li>Updated links and text at the top of search page.</li>
</ul>

<h2>v1.0.12 <small>2013-10-23</small></h2>
<ul>
	<li>
		For GeoJSON(P) when timezone (tz) data does not exist, a null timezone
		value is returned.
	</li>
	<li>
		Fixed region searches. Fractional numbers are allowable for
		latitude/longitude searches.
	</li>
	<li>Fixed circle searches.</li>
</ul>

<h2>v1.0.11 <small>2013-09-04</small></h2>
<ul>
	<li>Fixed search form so CSV/KML/QuakeML/GeoJSON work properly.</li>
</ul>

<h2>v1.0.10 <small>2013-09-03</small></h2>
<ul>
	<li>Added event-type information to all feed formats.</li>
	<li>
		New <a href="<?php print $SEARCH_PATH; ?>/">search form location</a>,
		layout, and features.
	</li>
	<li>
		Count method now properly reports number of earthquakes when query
		includes limit parameter.
	</li>
	<li>
		Can now search by multiple event types using comma-separated input values.
	</li>
	<li>
		New
		<a href="<?php print $FDSN_URL; ?>/application.json">application.json</a>
		method for fetching available values for enumerated input fields.
	</li>
	<li>
		Deleted products now excluded from results unless specifically requested.
	</li>
	<li>
		Can now specify min/max radius for circle searches in kilometers using
		new <a href="<?php print $FDSN_URL; ?>/#minradiuskm">minradiuskm</a> and
		<a href="<?php print $FDSN_URL; ?>/#maxradiuskm">maxradiuskm</a> parameters.
	</li>
</ul>

<h2>v1.0.9 <small>2013-07-29</small></h2>
<ul>
	<li>Implemented &ldquo;count&rdquo; method for FDSN queries.</li>
	<li>Updated feed descriptions to reference feed lifecycle policy.</li>
	<li>Corrected documentation for Tsunami flag usage.</li>
	<li>
		Now supports QuakeML output format for data feeds as well as searches.
	</li>
</ul>

<h2>v1.0.8 <small>2013-06-25</small></h2>
<ul>
	<li>
		Fixed problem with timestamps for events occurring prior to the epoch.
	</li>
	<li>
		Changed how catalog, contributor parameters are searched. Fixed logic error
		in query, which potentially excluded events.
	</li>
</ul>

<h2>v1.0.7 <small>2013-06-11</small></h2>
<ul>
	<li>Optimized performance for larger searches.</li>
</ul>

<h2>v1.0.6 <small>2013-06-11</small></h2>
<ul>
	<li>Improved join performance</li>
	<li>
		Added &ldquo;jsonerror&rdquo; parameter to web service, supporting json
		formatted error output.
	</li>
	<li>Stop escaping ampersands in GeoJSON &ldquo;detail&rdquo; urls.</li>
	<li>Updated Quakeml eventParameters to be unique per request.</li>
</ul>

<h2>v1.0.5 <small>2013-05-30</small></h2>
<ul>
	<li>Fixed output for KML format searches.</li>
	<li>
		GeoJSON format &ldquo;bbox&rdquo; property now reflects actual data
		extent rather than input query extent. This affects both feeds and
		searches.
	</li>
	<li>
		GeoJSON format now includes a &ldquo;type&rdquo; property indicating
		the type of seismic event for each feature.
	</li>
	<li>ATOM format now includes link to CAP alerts when available.</li>
</ul>

<h2>v1.0.4 <small>2013-05-20</small></h2>
<ul>
	<li>Display USGS logo and earthquake marker images inside the KML feed.</li>
</ul>

<h2>v1.0.0 <small>2013-04-30</small></h2>
<ul>
	<li>Eliminated HTML output format.</li>
	<li>
		Converted Numeric GeoJSON detail and summary properties from Strings to
		Numbers.
	</li>
	<li>Eliminated redundant GeoJSON detail properties.</li>
	<li>Added KML search output format.</li>
	<li>Separated KML feeds into separate feeds.</li>
</ul>
