<?php

$TITLE = "Change Log";
include_once '../conf/feed.inc.php';
$FDSN_URL = $FDSN_HOST . $FDSN_PATH;
include_once($_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php');

?>

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
		Fixed region searches. Fractional numbers are allowable for latitude/
		longitude searches.
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
		New <a href="<?php print $FDSN_URL; ?>/application.json">application.json</a>
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
	<li>Now supports QuakeML output format for data feeds as well as searches.</li>
</ul>

<h2>v1.0.8 <small>2013-06-25</small></h2>
<ul>
	<li>
		Fixed problem with timestamps for events occurring prior to the
		epoch.
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
    <li>Added &ldquo;jsonerror&rdquo; parameter to web service, supporting json formatted error output</li>
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
	<li>Converted Numeric GeoJSON detail and summary properties from Strings to Numbers.</li>
	<li>Eliminated redundant GeoJSON detail properties.</li>
	<li>Added KML search output format.</li>
	<li>Separated KML feeds into separate feeds.</li>
</ul>
