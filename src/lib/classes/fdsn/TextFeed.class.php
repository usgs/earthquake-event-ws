<?php

class TextFeed extends AbstractFeed {

  public function getMimeType() {
    return 'text/plain; charset=utf-8';
  }

  public function getHeader ($query=null) {
    return "#EventID|Time|Latitude|Longitude|Depth/km|Author|Catalog|Contributor|ContributorID|MagType|Magnitude|MagAuthor|EventLocationName\n";
  }

  public function getEntry ($event) {
    if ($event == null) { return ''; }

    $author = $event['origin_source'];
    $author = strtolower($author ? $author : $event['source']);

    $magAuthor = $event['magnitude_source'];
    $magAuthor = strtolower($magAuthor ? $magAuthor : $event['source']);

    return implode('|', array(
      $event['eventSource'] . $event['eventSourceCode'],
      $this->formatter->formatDateIso($event['eventTime'], null, ''),
      $event['eventLatitude'],
      $event['eventLongitude'],
      $event['eventDepth'],
      $author,
      $event['eventSource'],
      $event['source'],
      $event['eventSource'] . $event['eventSourceCode'],
      $event['magnitude_type'],
      $event['eventMagnitude'],
      $magAuthor,
      $event['region']
    )) . "\n";
  }

  public function getFooter () {
    return '';
  }

}

?>
