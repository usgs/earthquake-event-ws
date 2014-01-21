<?php

$TITLE = "Change Log";
include_once($_SERVER['DOCUMENT_ROOT'] . '/template/template.inc.php');

?>

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
