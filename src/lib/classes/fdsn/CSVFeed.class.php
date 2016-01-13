<?php

class CSVFeed extends AbstractFeed {

  public function getMimeType() {
    return 'text/csv';
  }

  public function getHeader ($query=null) {
    return implode(',', array(
      'time',
      'latitude',
      'longitude',
      'horizontalError',
      'depth',
      'depthError',
      'mag',
      'magType',
      'magError',
      'magNst',
      'nst',
      'gap',
      'dmin',
      'rms',
      'type',
      'status',
      'net',
      'id',
      'locationSource',
      'magSource',
      'updated',
      'place'
    )) . "\n";
  }

  public function getEntry ($event) {
    if ($event == null) { return ''; }

    return implode(",", array(
      $this->formatter->formatDateIso($event['eventTime']),
      $event['eventLatitude'],
      $event['eventLongitude'],
      $event['horizontal_error'],
      $event['eventDepth'],
      $event['vertical_error'],
      $event['eventMagnitude'],
      $event['magnitude_type'],
      $event['magnitude_error'],
      $event['magnitude_num_stations_used'],
      $event['num_stations_used'],
      $event['azimuthal_gap'],
      $event['minimum_distance'],
      $event['standard_error'],
      $event['event_type'],
      strtolower($event['review_status']),
      $event['eventSource'],
      $event['eventSource'] . $event['eventSourceCode'],
      $event['origin_source']
          ? strtolower($event['origin_source'])
          : $event['eventSource'],
      $event['magnitude_source']
          ? strtolower($event['magnitude_source'])
          : $event['eventSource'],
      $this->formatter->formatDateIso($event['eventUpdateTime']),
      '"' . str_replace('"', '""', $event['region']) . '"'
    )) . "\n";
  }

  public function getFooter () {
    return '';
  }

}

?>
