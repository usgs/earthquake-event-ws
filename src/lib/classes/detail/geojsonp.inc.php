<?php


header('Content-type: text/javascript');
if($event == null ) {
  header('HTTP/1.0 404 Not Found');
  print 'eqfeed_callback({"message": "Event not found."})';
  return;
} else if ($event->isDeleted()) {
  header('HTTP/1.0 404 Not Found');
  print 'eqfeed_callback({"message": "Event deleted."})';
  return;
}

global $storage;


$event_array = $event->toArray($storage);

// add event summary for region, and other summarized properties
// this is hacky and slow, but effective
$query = new ProductIndexQuery();
$query->setEventSource($event->getSource());
$query->setEventSourceCode($event->getSourceCode());
$summaries = $index->getEventSummaries($query);
$summary = null;
if (isset($summaries[$event->getIndexId()])) {
  $summary = $summaries[$event->getIndexId()];
}

$array = array(
  'type' => 'Feature',
  'properties' => array(
    'mag' => safefloatval($summary->getMagnitude()),
    'place' => $summary->properties["region"],
    'time' => $summary->getTime(),
    'updated' => $summary->getLastModified(),
    'tz' => safeintval($summary->getOffset()),
    'url' => $summary->getDetailLink(),
    'felt' => safeintval($summary->properties['num_responses']),
    'cdi' => safefloatval($summary->properties['maxcdi']),
    'mmi' => safefloatval($summary->properties['maxmmi']),
    'alert' => $summary->properties['alertlevel'],
    'status' => $summary->properties['review_status'],
    'tsunami' => intval($summary->getTsunami()),
    'sig' => safeintval($summary->getSignificance()),
    'net' => $summary->getSource(),
    'code' => $summary->getSourceCode(),
    'ids' => $summary->properties['eventids'],
    'sources' => $summary->properties['eventsources'],
    'types' => $summary->properties['types'],
    'nst' => safeintval($summary->getNumStationsUsed()),
    'dmin' => safefloatval($summary->getMinimumDistance()),
    'rms' => safefloatval($summary->getStandardError()),
    'gap' => safefloatval($summary->getAzimuthalGap()),
    'magType' => $summary->getMagnitudeType(),
    'type' => $summary->getEventType(),
    'title' => $summary->getTitle()
  ),
  'geometry' => array(
    'type' => 'Point',
    'coordinates' => array(
      floatval($summary->getLongitude()),
      floatval($summary->getLatitude()),
      safefloatval($summary->getDepth())
    )
  ),
  'id' => $summary->getSource() . $summary->getSourceCode()
);

$array['properties']['products'] = $event_array['products'];


if (!isset($callback)) {
  $callback = 'eqfeed_callback';
}
echo $callback . '(';
$json = str_replace('\/', '/', json_encode($array));
echo preg_replace('/"(time|indexTime|updated|updateTime|lastModified)":"(-?[\d]+)"/', '"$1":$2', $json);
echo ');';
