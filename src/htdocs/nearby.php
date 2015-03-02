<?php

  // read configuration
  include_once '../conf/feed.inc.php';



  // parse parameters
  $action = param('action', 'help');
  $latitude = param('latitude', false);
  $longitude = param('longitude', false);
  $eventtime = param('time', false);
  $magnitude = param('magnitude', false);
  $depth = param('depth', false);
  $eventSource = param('eventSource', false);
  $windowSize = param('windowSize', 1.0);



  // ASSOCIATION CONSTANTS

  // time within 16 seconds
  $TIME_DIFF_SECONDS = 16;
  // location within 100 kilometers
  $LOCATION_DIFF_KILOMETERS = 100;
  // magnitude within .5
  $MAGNITUDE_DIFF = 0.5;
  // depth within 50 kilometers
  $DEPTH_DIFF = 50;

  // convert distance in kilometers to distance in degrees
  $KILOMETERS_PER_DEGREE = 111.2;



  // SEARCH: lat, lon, time are required
  if ($action == 'search' && $latitude !== false && $longitude !== false && $eventtime !== false) {

    $query = new FDSNQuery();

    // time box
    $eventtime = strtotime($eventtime);
    $timeDiff = $windowSize * $TIME_DIFF_SECONDS;
    $query->starttime = ($eventtime - $timeDiff) . '000';
    $query->endtime = ($eventtime + $timeDiff) . '000';

    // latitude box
    $latDiff = $windowSize * $LOCATION_DIFF_KILOMETERS / $KILOMETERS_PER_DEGREE;
    $query->minlatitude = max(-90, $latitude - $latDiff);
    $query->maxlatitude = min(90, $latitude + $latDiff);

    // longitude box
    if (abs($latitude) < 89) {
      // make allowed difference larger closer to poles
      $lonDiff = $latDiff / cos(deg2rad($latitude));

      $query->minlongitude = $longitude - $lonDiff;
      $query->maxlongitude = $longitude + $lonDiff;
    }


    // TODO: magnitude, depth boxes
    if ($magnitude !== false) {
      $magnitude = floatval($magnitude);
      $magDiff = $windowSize * $MAGNITUDE_DIFF;
      $query->minmagnitude = $magnitude - $magDiff;
      $query->maxmagnitude = $magnitude + $magDiff;
    }

    if ($depth !== false) {
      $depth = floatval($depth);
      $depthDiff = $windowSize * $DEPTH_DIFF;
      $query->mindepth = $depth - $depthDiff;
      $query->maxdepth = $depth + $depthDiff;
    }

    if ($eventSource !== false) {
      $query->catalog = $eventSource;
    }


    $query->resultTitle = 'USGS Earthquakes near ' . 
      abs($latitude) . '&deg;' . ($latitude > 0 ? "N" : "S") . ' ' .
      abs($longitude) . '&deg;' . ($longitude > 0 ? "E" : "W") . ' ' .
      'at ' . gmdate('Y-m-d\TH:i:s\Z', $eventtime);


    // function to sort earthquakes by nearest to search center
    function sortByNearest($a, $b) {
      // search center
      global $latitude;
      global $longitude;
      global $eventtime;
      // search extents
      global $latDiff;
      global $timeDiff;


      $atime = (floatval($a['eventTime'])/1000 - $eventtime) / $timeDiff;
      $alat = (floatval($a['eventLatitude'])/1000 - $latitude) / $latDiff;
      $alon = (floatval($a['eventLongitude'])/1000 - $longitude) / $latDiff;
      $adist = sqrt($atime*$atime + $alat*$alat + $alon*$alon);

      $btime = (floatval($b['eventTime'])/1000 - $eventtime) / $timeDiff;
      $blat = (floatval($b['eventLatitude'])/1000 - $latitude) / $latDiff;
      $blon = (floatval($b['eventLongitude'])/1000 - $longitude) / $latDiff;
      $bdist = sqrt($btime*$btime + $blat*$blat + $blon*$blon);

      if ($adist > $bdist) {
        return 1;
      } else if ($adist < $bdist) {
        return -1;
      } else {
        return 0;
      }
    }

    // sort results by nearest to center
    $results = $fdsnIndex->getEvents($query);
    usort($results, "sortByNearest");

    // output results
    $callback = new FDSNIndexCallback();
    $callback->feed = new GeoJSONFeed();
    $callback->onStart($query);
    foreach ($results as $event) {
      $callback->onEvent($event, $fdsnIndex);
    }
    $callback->onEnd();


    exit();
  }



// HELP

?>


<style>
  dt, code { 
    font-family: monospace;
  }
  dd {
    margin-bottom:1em;
  }
</style>


<h1>Search For Earthquakes near a Location and Time.</h1>

<h2>Parameters</h2>

<dl>
  <dt>action</dt>
  <dd>
    Required.
    To search action must be set to "search".
    Any other action will show this help page.
  </dd>

  <dt>latitude</dt>
  <dd>
    Required.
    Latitude to search around.
    Decimal degrees, [-90, 90].
  </dd>

  <dt>longitude</dt>
  <dd>
    Required.
    Longitude to search around.
    Decimal degrees, (-180, 180].
  </dd>

  <dt>time</dt>
  <dd>
    Required.
    Time to search around.
    This parameter is parsed using <a href="http://php.net/manual/en/function.strtotime.php">php's strtotime() function</a>.
    <p>ISO8601 timestamp is recommended (<?php echo str_replace("+00:00", "Z", gmdate("c")); ?>), because it is unambiguous; you have been warned.
    <br/><small>NOTE: if using an ISO8601 timezone other than "Z" for UTC (i.e. &plusmn;HH:MM) be sure to url encode parameters properly.</small></p>
  </dd>

  <dt>magnitude</dt>
  <dd>Optional.  Magnitude to search around.</dd>

  <dt>depth</dt>
  <dd>Optional.  Depth to search around.</dd>

  <dt>eventSource</dt>
  <dd>Optional.  Only search for events contributed to by this source.</dd>

  <dt>windowSize</dt>
  <dd>Optional.
    Size of association window.
    Default=1.0.
  </dd>
</dl>



<h2>Association window</h2>
<p>These are the default window sizes are used.  Changing the windowSize parameter will linearly scale the windowSize (i.e. window * windowSize)</p>

<dl>
  <dt>time</dt>
  <dd><code>time &plusmn; 16 seconds</code></dd>

  <dt>location</dt>
  <dd>&plusmn; 100 kilometers.
    <ul>
      <li><code>latitude &plusmn; (100 kilometers / 111.2 kilometers/degree)</code></li>
      <li><code>longitude &plusmn; ((100 kilometers / 111.2 kilometers/degree) / cos(latitude))</code>
        <br/><small>longitude is not filtered when abs(<code>latitude</code>) &ge; 89</small>
      </li>
    </ul>
  </dd>

  <dt>magnitude</dt>
  <dd><code>magnitude &plusmn; 0.5</code>.</dd>

  <dt>depth</dt>
  <dd><code>depth &plusmn; 50 kilometers</code>.</dd>
</dl>



<h2>Output</h2>
<p>Output matches the <a href="api/geojson.php">GeoJSON summary format</a>; although sort order has been modified to place earthquakes with the smallest "euclidean distance" from the center search point first.</p>

<p>"euclidean distance" is defined as:  <code>sqrt( ((eqLatitude-latitude)/latitudeWindowRadius)^2 + ((eqLongitude-longitude)/latitudeWindowRadius)^2 + ((eqTime-time)/timeWindowRadius)^2 )</code></p>