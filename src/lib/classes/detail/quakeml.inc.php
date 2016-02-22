<?php


header('Content-type: application/json');
if( $event == null ) {
  header('HTTP/1.0 404 Not Found');
  print '{"message": "Event not found."}';
  return;
}


/* global $query, $event */

$products = $event->products;
$origins = isset($products['origin']) ? $products['origin'] : array();
$origins = Event::getSortedMostPreferredFirst(Event::getWithoutSuperseded($origins));
$phases = isset($products['phase-data']) ? $products['phase-data'] : array();
$phases = Event::getSortedMostPreferredFirst(Event::getWithoutSuperseded($phases));

// find corresponding phase-data/origin product
$eventid = $query->eventid;
$summary = null;
// check phase-data
foreach ($phases as $phase) {
  if ($phase->getEventId() === $eventid) {
    $summary = $phase;
    break;
  }
}
if ($summary === null) {
  // no matching phase-data, check origins
  foreach ($origins as $origin) {
    if ($origin->getEventId() === $eventid) {
      $summary = $origin;
      break;
    }
  }
}
if ($summary !== null) {
  global $storage;
  // load product contents
  $product = $storage->getProduct($summary->getId());
  if ($product !== null) {
    $contents = $product->getContents();
    if ($contents !== null && isset($contents['quakeml.xml'])) {
      $quakeml = $contents['quakeml.xml']->getContent();
      header('Content-type: application/xml');
      echo $quakeml;
      exit();
    }
  }
}


// if we got here, unable to serve quakeml detail for requested eventid from
// phase-data/origin product, fall back to summary formatter
global $fdsnIndex;
global $service;
if (!isset($service)) {
  $service = new FDSNEventWebService($fdsnIndex);
}
$service->handleSummaryQuery($query);
