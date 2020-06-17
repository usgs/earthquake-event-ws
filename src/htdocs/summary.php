<?php

include_once '../conf/feeds.inc.php';

// requested url
$URL = $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'];
$PLATES = 'https://earthquake.usgs.gov/learn/plate-boundaries.kmz';

// name => age in seconds
$AGES = array(
  'hour' => 3600,
  'day' => 86400,
  'week' => 604800,
  'month' => 2592000
);

// name => size in magnitude
$SIZES = array(
  'all' => null,
  '1.0' => 0.95,
  '2.5' => 2.45,
  '4.5' => 4.45,
  'significant' => null
);

$FORMATS = array('geojson', 'geojsonp', 'atom', 'kml', 'csv', 'quakeml',
    'text', 'xml');

$KML_COLORBYS = array('age', 'depth');





// parse arguments and build query
$query = new FDSNQuery();
$networkLink = false;

try {
  $params = param('params', false);
  $params = explode("_", $params);

// format
  $format = param('format', false);
  if (!$format || !in_array($format, $FORMATS)) {
    throw new Exception("Unknown format");
  }
  if ($format === 'geojsonp') {
    // geojsonp is geojson with implied callback name
    $query->format = 'geojson';
    $query->callback = 'eqfeed_callback';
  } else {
    $query->format = $format;
  }

// kml extras
  if ($format === 'kml') {
    // look for additional parameters
    $colorby = $params[2];
    if (!in_array($colorby, $KML_COLORBYS)) {
      throw new Exception("Unknown kml colorby");
    }
    $query->kmlcolorby = $colorby;
    // use magnitude bins, largest first
    $query->orderby = 'magnitude';

    // read optional kml params
    for($i=3, $len=count($params); $i<$len; $i++) {
      $param = $params[$i];

      if ($param === 'animated') {
        $query->kmlanimated = true;
      } else if ($param === 'link') {
        // serve network link to kml
        $networkLink = true;
      }
    }
  }

// age
  $age = $params[1];
  if (!array_key_exists($age, $AGES)) {
    throw new Exception("Unknown age");
  }
  // starttime is milliseconds
  $query->starttime = (time() - $AGES[$age]) . '000';


// size
  $size = $params[0];
  if (!array_key_exists($size, $SIZES)) {
    throw new Exception("Unknown size");
  }
  $query->minmagnitude = $SIZES[$size];


// significance
  if ($size === 'significant') {
    $query->minsig = 600;
  }


// caching headers
  // per llastowka: cache for 60 seconds regardless of age
  $CACHE_MAXAGE = 60;
  include $APP_DIR . '/lib/cache.inc.php';


//title
  $title = 'USGS';
  if ($size === 'significant') {
    $title .= ' Significant';
  } else if ($size === 'all') {
    $title .= ' All';
  } else {
    $title .= ' Magnitude ' . $size . '+';
  }
  if ($SCENARIO_MODE) {
    $title .= ' Scenarios';
  } else {
    $title .= ' Earthquakes';
  }
  $title .= ', Past ' . ucfirst($age);
  $query->resultTitle = $title;


} catch (Exception $e) {
  header('Status: 404 Not Found');
  header('Content-type: text/plain');
  echo '404 File Not Found';

  // log error
  trigger_error($e->getMessage());

  exit();
}



if ($networkLink) {
  // serve kml network link
  header('Content-type: application/vnd.google-earth.kml+xml');
  echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
    '<kml xmlns="http://www.opengis.net/kml/2.2">' .
    '<Document>' .
      '<name>Earthquakes</name>' .
      '<visibility>1</visibility>' .
      '<open>1</open>' .
      '<LookAt>' .
        '<longitude>-100</longitude>' .
        '<latitude>39</latitude>' .
        '<range>4000000</range>' .
        '<tilt>0</tilt>' .
        '<heading>0</heading>' .
      '</LookAt>' .
      '<NetworkLink>' .
        '<visibility>1</visibility>' .
        '<refreshVisibility>0</refreshVisibility>' .
        '<open>1</open>' .
        '<name>' . $query->resultTitle . '</name>' .
        '<Snippet maxLines="1">Updates every ' . intval($CACHE_MAXAGE / 60) . ' minutes</Snippet>' .
        '<Link>' .
          '<href>' . str_replace('_link', '', $URL) . '</href>' .
          '<refreshMode>onInterval</refreshMode>' .
          '<refreshInterval>' . $CACHE_MAXAGE . '</refreshInterval>' .
        '</Link>' .
      '</NetworkLink>' .
      '<NetworkLink>' .
        '<name>Tectonic Plates</name>'.
        '<visibility>0</visibility>' .
        '<refreshVisibility>0</refreshVisibility>' .
        '<Link>' .
          '<href>' .
            $PLATES .
          '</href>' .
        '</Link>' .
      '</NetworkLink>' .
    '</Document>' .
    '</kml>';
  exit();
}



// serve summary feed
$service = new FDSNEventWebService($fdsnIndex);
// increase limit for summary feeds
$service->serviceLimit = 30000;
try {
  $service->handleSummaryQuery($query);
} catch (Exception $e) {
  $service->error(503, $e->getMessage());
}
