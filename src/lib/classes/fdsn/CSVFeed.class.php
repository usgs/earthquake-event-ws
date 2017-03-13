<?php

class CSVFeed extends AbstractFeed {

  public function getMimeType() {
    return 'text/csv; charset=utf-8';
  }

  public function getHeader ($query=null) {
    return implode(',', array(
      'time',
      'latitude',
      'longitude',
      'depth',
      'mag',
      'magType',
      'nst',
      'gap',
      'dmin',
      'rms',
      'net',
      'id',
      'updated',
      'place',
      'type',
      'horizontalError',
      'depthError',
      'magError',
      'magNst',
      'status',
      'locationSource',
      'magSource'
    )) . "\n";
  }

  public function getEntry ($event) {
    if ($event == null) { return ''; }

    return implode(",", array(
      $this->formatter->formatDateIso($event['eventTime']),
      $event['eventLatitude'],
      $event['eventLongitude'],
      $event['eventDepth'],
      $event['eventMagnitude'],
      $event['magnitude_type'],
      $event['num_stations_used'],
      $event['azimuthal_gap'],
      $event['minimum_distance'],
      $event['standard_error'],
      $event['eventSource'],
      $event['eventSource'] . $event['eventSourceCode'],
      $this->formatter->formatDateIso($event['eventUpdateTime']),
      '"' . str_replace('"', '""', $event['region']) . '"',
      $event['event_type'],
      $event['horizontal_error'],
      $event['vertical_error'],
      $event['magnitude_error'],
      $event['magnitude_num_stations_used'],
      strtolower($event['review_status']),
      $event['origin_source']
          ? strtolower($event['origin_source'])
          : $event['eventSource'],
      $event['magnitude_source']
          ? strtolower($event['magnitude_source'])
          : $event['eventSource']
    )) . "\n";
  }

  public function getFooter () {
    return '';
  }

}

?>
