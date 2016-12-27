<?php

class AtomFeed extends AbstractFeed {

  public function getMimeType() {
    return 'application/xml';
  }

  public function getFeedUrl() {
    global $HOST_URL_PREFIX;
    return $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'];
  }

  public function getHeader ($query) {
    $self = $this->getFeedUrl();
    return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
      '<feed xmlns="http://www.w3.org/2005/Atom"' .
          ' xmlns:georss="http://www.georss.org/georss">' .
        '<title>' . $query->resultTitle . '</title>' .
        '<updated>' . date('Y-m-d\TH:i:s\Z', time()) . '</updated>' .
        '<author>' .
          '<name>U.S. Geological Survey</name>' .
          '<uri>https://earthquake.usgs.gov/</uri>' .
        '</author>' .
        '<id>' . $self . '</id>' .
        '<link rel="self" href="' . htmlentities($self) . '"/>' .
        '<icon>https://earthquake.usgs.gov/favicon.ico</icon>' . "\n";
  }

  public function getEntry ($event = null) {
    if ($event == null) { return ''; }

    return '<entry>' .
        '<id>urn:earthquake-usgs-gov:' . $event['eventSource'] . ':' . $event['eventSourceCode'] . '</id>' .
        '<title>' . $this->getEventTitle($event) . '</title>' .
        '<updated>' . $this->formatter->formatDateIso($event['eventUpdateTime']) . '</updated>' .
        '<link rel="alternate" type="text/html" href="' .
            self::getEventDetailLink($event['eventSource'] . $event['eventSourceCode']) .
          '"/>' .
        $this->getCapLink($event) .
        '<summary type="html">' .
          '<![CDATA['. $this->getQuickSummary($event, true) .']]>' .
        '</summary>' .
        '<georss:point>' . $event['eventLatitude'] . ' ' . $event['eventLongitude'] . '</georss:point>' .
        ($event['eventDepth'] != '' ?
          '<georss:elev>' . (-1000*$event['eventDepth']) . '</georss:elev>'
          : '') .
        '<category label="Age" term="' . $this->getEventAge($event) . '"/>' .
        '<category label="Magnitude" term="Magnitude ' .
            ($event['eventMagnitude'] == '' ?
                '?'
              : intval($event['eventMagnitude'])) .
          '"/>' .
        '<category label="Contributor" term="' . $event['source'] . '"/>' .
        '<category label="Author" term="' . $event['eventSource'] . '"/>' .
      '</entry>' . "\n";
  }

  public function getFooter () {
    return '</feed>';
  }

  /**
   * This requires CAP alerts to be sent through PDL using the type "cap". This
   * can be changed.
   */
  private function getCapLink ($event = null) {
    $link = '';
    if ($event !== null && strpos($event['producttypes'], ',cap,') !== FALSE) {
      $link = '<link rel="alternate" type="application/cap+xml" href="' .
          self::getEventDetailFeed($event['eventSource'] . $event['eventSourceCode'],
              'cap') .
          '"/>';
    }
    return $link;
  }

  public function getEventAge ($event = null) {
    if ($event == null) { return ''; }

    $now = time();
    $then = intval(substr($event['eventTime'], 0, -3));
    $age = $now - $then;

    if ($age <= 3600) {
      return 'Past Hour';
    } else if ($age <= 86400) {
      return 'Past Day';
    } else if ($age <= 604800) {
      return 'Past Week';
    } else if ($age <= 2592000) {
      return 'Past Month';
    } else {
      return 'More than one month';
    }
  }

}
?>
