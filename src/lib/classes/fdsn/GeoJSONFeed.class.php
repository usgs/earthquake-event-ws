<?php

/**
 * JSONFeed is a JSON or JSONP feed of earthquakes.
 *
 * The JSONP feed does not accept a callback name as a paramter,
 * unlike typical JSONP requests.  This is for cacheability: everybody makes
 * the same request.
 */
class GeoJSONFeed extends AbstractFeed {

  protected $jsonp;
  protected $callback;


  // track whether this is the first entry,
  // so commas can be added between entries.
  private $firstEntry = true;

  // track data extents for bbox
  protected $minLongitude = 180;
  protected $maxLongitude = -180;
  protected $minLatitude = 90;
  protected $maxLatitude = -90;
  protected $minDepth = 1000;
  protected $maxDepth = -100;

  /**
   * Construct a new JSON Feed object.
   *
   * @param $jsonp boolean, default false, whether this should
   *                        be jsonp output (true),
   *                        or json (false).
   * @param $callback string, default 'eqfeed_callback', name of
   *                        jsonp function to call with json data.
   */
  public function __construct($jsonp=false, $callback='eqfeed_callback') {
    $this->jsonp = $jsonp;
    $this->callback = $callback;
    parent::__construct();
  }

  public function getMimeType() {
    if ($this->jsonp) {
      return 'text/javascript; charset=utf-8';
    } else {
      return 'application/json; charset=utf-8';
    }
  }

  public function getHeader ($query) {
    $header = '';

    if ($this->jsonp) {
      // start callback
      $header .= $this->callback . '(';
    }

    // server portion of url
    global $HOST_URL_PREFIX;

    // the FDSNEventWebService
    global $service;


    $metadata = array();
    $metadata['generated'] = time() . '000';
    $metadata['url'] = $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'];
    $metadata['title'] = $query->resultTitle;
    $metadata['status'] = 200;
    if (!empty($service)) {
      $metadata['api'] = $service->version;
    }
    if ($query->limit !== null) {
      $metadata['limit'] = $query->limit;
      $metadata['offset'] = $query->offset;
    }
    if ($query->resultCount !== null) {
      $metadata['count'] = $query->resultCount;
    }

    $json = str_replace('\/', '/', safe_json_encode($metadata));

    // data is an array of entries
    $header .= '{"type":"FeatureCollection",' .
      '"metadata":' . preg_replace('/"(generated)":"(-?[\d]+)"/', '"$1":$2', $json) . ',';

    // start features array
    $header .= '"features":[';

    return $header;
  }

  public function getEntry ($event) {
    $entry = '';
    if ($this->firstEntry) {
      $this->firstEntry = false;
    } else {
      $entry .= ",\n";
    }

    $id = $event['eventSource'] . $event['eventSourceCode'];
    $event['eventpage_url'] = self::getEventDetailLink($id);
    $event['detail_url'] = self::getEventDetailFeed($id, ($this->jsonp?"geojsonp":"geojson"));

    $longitude = floatval($event['eventLongitude']);
    $latitude = floatval($event['eventLatitude']);
    $depth = safefloatval($event['eventDepth']);
    $type = $event['event_type'];

    if ($type === null || $type === '') {
      $type = 'earthquake';
    }

    if ($longitude < $this->minLongitude) { $this->minLongitude = $longitude; }
    if ($longitude > $this->maxLongitude) { $this->maxLongitude = $longitude; }
    if ($latitude < $this->minLatitude) { $this->minLatitude = $latitude; }
    if ($latitude > $this->maxLatitude) { $this->maxLatitude = $latitude; }
    if (is_numeric($depth) && $depth < $this->minDepth) {
      $this->minDepth = $depth;
    }
    if (is_numeric($depth) && $depth > $this->maxDepth) {
      $this->maxDepth = $depth;
    }

    $array = array(
      'type' => 'Feature',
      'properties' => array(
        'mag' => safefloatval($event['eventMagnitude']),
        'place' => $event["region"],
        'time' => $event['eventTime'],
        'updated' => $event['eventUpdateTime'],
        'tz' => safeintval($event['offset']),
        'url' => $event['eventpage_url'],
        'detail' => $event['detail_url'],
        'felt' => safeintval($event['num_responses']),
        'cdi' => safefloatval($event['maxcdi']),
        'mmi' => safefloatval($event['maxmmi']),
        'alert' => $event['alertlevel'],
        'status' => ($event['eventStatus'] === 'DELETE'
            ? 'deleted'
            : $event['review_status']),
        'tsunami' => intval($event['tsunami']),
        'sig' => safeintval($event['significance']),
        'net' => $event['eventSource'],
        'code' => $event['eventSourceCode'],
        'ids' => $event['eventids'],
        'sources' => $event['eventsources'],
        'types' => $event['producttypes'],
        'nst' => safefloatval($event['num_stations_used']),
        'dmin' => safefloatval($event['minimum_distance']),
        'rms' => safefloatval($event['standard_error']),
        'gap' => safefloatval($event['azimuthal_gap']),
        'magType' => $event['magnitude_type'],
        'type' => $type,
        'title' => $this->getEventTitle($event)
      ),
      'geometry' => array(
        'type' => 'Point',
        'coordinates' => array(
          $longitude,
          $latitude,
          $depth
        )
      ),
      'id' => $id
    );

    $json = str_replace('\/', '/', safe_json_encode($array));
    $entry .= preg_replace('/"(time|updated)":"(-?[\d]+)"/', '"$1":$2', $json);

    return $entry;
  }

  public function getFooter () {
    $footer = '';

    // end array of entries
    $footer .= ']';

    // add bounding box, if it makes sense
    if (
      $this->minLongitude < $this->maxLongitude
      && $this->minLatitude < $this->maxLatitude
      && $this->minDepth < $this->maxDepth
    ) {
      $footer .= ',"bbox":[' .
          $this->minLongitude . ',' . $this->minLatitude . ',' . $this->minDepth . ',' .
          $this->maxLongitude . ',' . $this->maxLatitude . ',' . $this->maxDepth .
        ']';
    }

    // end feed object
    $footer .= '}';

    if ($this->jsonp) {
      // end callback
      $footer .= ');';
    }

    return $footer;
  }

}

?>
