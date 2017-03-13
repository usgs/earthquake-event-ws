<?php


/**
 * KML Feed, based on REQS_MYSQL/kml3.pl
 */
class KMLFeed extends AbstractFeed {

  private $colorBy;
  private $animated;

  private $folder = null;
  private $query = null;

  public $useFolders = true;

  public function getMimeType() {
    return 'application/vnd.google-earth.kml+xml; charset=utf-8';
  }

  public function getHeader ($query) {
    $this->query = $query;
    $this->colorBy = $query->kmlcolorby;
    $this->animated = $query->kmlanimated;


    global $HOST_URL_PREFIX;
    $feedBaseUrl = self::getFeedUrlPrefix();
    $feedUrl = $HOST_URL_PREFIX . htmlentities($_SERVER['REQUEST_URI']);

    $header = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom" xml:lang="en-US">
<NetworkLinkControl>
  <minRefreshPeriod>60</minRefreshPeriod>
</NetworkLinkControl>
<Document id="' . $feedUrl . '">
  <name>' . $query->resultTitle . '</name>
  <description>Updated ' . $this->formatter->formatDate(time() . '000') . '</description>
  <atom:author><atom:name>U.S. Geological Survey</atom:name></atom:author>
  <atom:link href="' . $feedUrl . '" rel="self"/>
';

    $balloonStyle = $this->getBalloonStyle();
    $header .= '<Style id="circle-inactive">' .
        '<IconStyle><Icon><href>' .
          $feedBaseUrl . '/images/kml_circle.png' .
        '</href></Icon></IconStyle>' .
        '<LabelStyle><scale>0</scale></LabelStyle>' .
        $balloonStyle .
      '</Style>' .
      '<Style id="circle-active">' .
        '<IconStyle><Icon><href>' .
          $feedBaseUrl . '/images/kml_circle.png' .
        '</href></Icon></IconStyle>' .
        '<LabelStyle><scale>1</scale></LabelStyle>' .
        $balloonStyle .
      '</Style>' .
      '<StyleMap id="circle">' .
        '<Pair><key>normal</key><styleUrl>#circle-inactive</styleUrl></Pair>' .
        '<Pair><key>highlight</key><styleUrl>#circle-active</styleUrl></Pair>' .
      '</StyleMap>' . "\n";

    $header .= $this->getLookAt();

    return $header;
  }

  public function getBalloonStyle() {
    $feedBaseUrl = self::getFeedUrlPrefix();

    $balloonStyle = '<BalloonStyle><text><![CDATA[' .
        '<style>' . $this->getQuickSummaryCSS() . '</style>' .
        '<img src="' . $feedBaseUrl . '/images/kml_banner.jpg" alt="USGS" width="400" height="40"/>' .
        '$[description]' .
        '<img src="' . $feedBaseUrl . '/images/anss.gif" alt="ANSS" width="100" height="31"/>' .
      ']]></text></BalloonStyle>';
    return $balloonStyle;
  }

  public function getLookAt($latitude=39, $longitude=-100, $range=4000000, $tilt=0, $heading=0) {
    return '<LookAt>' .
        '<longitude>' . $longitude . '</longitude>' .
        '<latitude>' . $latitude . '</latitude>' .
        '<range>' . $range . '</range>' .
        '<tilt>' . $tilt . '</tilt>' .
        '<heading>' . $heading . '</heading>' .
      '</LookAt>' . "\n";
  }

  public function startFolder($event) {
    if (!$this->useFolders) { return ''; }

    $eventFolder = null;
    if ($this->query->orderby === 'time' || $this->query->orderby === 'time-asc') {
      // group by day

      $eventFolder = gmdate("Y-m-d", substr($event['eventTime'], 0, -3));
      $title = $eventFolder;
    } else {
      // orderby === 'magnitude' or 'magnitude-asc';

      // group by magnitude
      if ($event['eventMagnitude'] < 0) {
        $eventFolder = '&lt;0';
      } else {
        $eventFolder = intval($event['eventMagnitude']);
      }
      $title = 'Magnitude ' . $eventFolder;
    }

    if ($this->folder === null || $this->folder !== $eventFolder) {
      $r = $this->stopFolder();
      $this->folder = $eventFolder;

      return $r . '<Folder>' .
          '<name>' . $title . '</name>' .
          '<open>0</open>';
    } else {
      return '';
    }
  }

  public function stopFolder() {
    if (!$this->useFolders) { return ''; }

    if ($this->folder !== null) {
      $this->folder = null;
      return "\n" . '</Folder>';
    } else {
      return '';
    }
  }


  public function getEntry ($event = null) {
    $entry = '';
    $entry .= $this->startFolder($event);

    $id = $event['eventSource'] . $event['eventSourceCode'];

    $entry .= "\n" . '<Placemark id="' . $id . '">';
    $entry .= '<name>' . $this->getEventTitle($event) . '</name>';

    $entry .= $this->getEntryDescription($event,
        array('<a href="' . str_replace('&', '&amp;', self::getEventDetailFeed($id, "kml")) . '">' .
            'Google Earth KML layers for this event' .
          '</a>')
      );

    $lon_google = $event['eventLongitude'];
    $lat_google = $event['eventLatitude'];

    $entry .= '<Snippet maxLines="0"></Snippet>';
    $entry .= "<LookAt><longitude>$lon_google</longitude><latitude>$lat_google</latitude><range>1000000</range></LookAt>";
    $entry .= "<styleUrl>#circle</styleUrl>";
    $entry .= "<Style><IconStyle>" .
          "<color>" . $this->getColor($event) . "</color>" .
          "<scale>" . $this->getSize($event) . "</scale>" .
        "</IconStyle></Style>";
    $entry .= "<Point><coordinates>$lon_google,$lat_google,0</coordinates></Point>";

    if ($this->animated) {
      $entry .= '<TimeStamp><when>' . $this->formatter->formatDateIso($event['eventTime']) . '</when></TimeStamp>';
    }

    $entry .= '</Placemark>';

    return $entry;
  }

  public function getEntryDescription($event, $links=null) {
    $id = $event['eventSource'] . $event['eventSourceCode'];

    $description = '<description><![CDATA[';

    $description .= '<h2>' . $this->getEventTitle($event) . '</h2>';
    $description .= $this->getQuickSummary($event);
    $description .= '<p class="links">' .
      '<a href="' . $this->getEventDetailLink($id) . '">Details from USGS web site</a>';
      if (is_array($links)) {
        $description .= '<br/>' . implode('<br/>', $links);
      }
    $description .= '</p>';

    $description .= ']]></description>';

    return $description;
  }

  public function getFooter () {
    $feedBaseUrl = $this->getFeedUrlPrefix();

    $footer = $this->stopFolder();
    $footer .= "\n";

    if ($this->colorBy == 'age') {
      $footer .= '<ScreenOverlay>' .
            '<name>Legend</name>' .
            '<Icon><href>' . $feedBaseUrl . '/images/kml_age_legend.png</href></Icon>' .
            '<overlayXY x="0" y="380" xunits="pixels" yunits="pixels"/>' .
            '<screenXY x="5" y="1" xunits="pixels" yunits="fraction"/>' .
            '<rotationXY x="0" y="0" xunits="pixels" yunits="pixels"/>' .
            '<size x="0" y="0" xunits="pixels" yunits="pixels"/>' .
          '</ScreenOverlay>';
    } else if ($this->colorBy == 'depth') {
      $footer .= '<ScreenOverlay>' .
            '<name>Legend</name>' .
            '<Icon><href>' . $feedBaseUrl . '/images/kml_depth_legend.png</href></Icon>' .
            '<overlayXY x="0" y="442" xunits="pixels" yunits="pixels"/>' .
            '<screenXY x="5" y="1" xunits="pixels" yunits="fraction"/>' .
            '<rotationXY x="0" y="0" xunits="pixels" yunits="pixels"/>' .
            '<size x="0" y="0" xunits="pixels" yunits="pixels"/>' .
          '</ScreenOverlay>';
    }

    $footer .= $this->getUSGSOverlay();
    $footer .= "\n" . '</Document></kml>';

    return $footer;
  }


  public function getUSGSOverlay() {
    return '<ScreenOverlay>' .
        '<name>USGS Logo</name>' .
        '<Icon><href>' . $this->getFeedUrlPrefix() . '/images/kml_usgs_logo.png</href></Icon>' .
        '<overlayXY x="1" y="0" xunits="fraction" yunits="pixels"/>' .
        '<screenXY x=".82" y="30" xunits="fraction" yunits="pixels"/>' .
        '<rotationXY x="0" y="0" xunits="pixels" yunits="pixels"/>' .
        '<size x="0" y="0" xunits="pixels" yunits="pixels"/>' .
      '</ScreenOverlay>';
  }

  public function getFeedUrl() {
    $feedBaseUrl = $this->getFeedUrlPrefix();
    return $feedBaseUrl . '/summary/' . $this->getMinMagnitude() . '_' . $this->getAgeString() . '_' . $this->colorBy . ($this->animated ? "_animated" : "") . '.kml';
  }


  public function getTitle() {
    $title = parent::getTitle();

    $title .= ', colored by ' . $this->colorBy;
    if ($this->animated) {
      $title .= ", animated";
    }

    return $title;
  }

  protected function getColor($e) {
    static $now = null;

    if ($this->colorBy == 'age') {
      if ($now == null) {
        $now = time();
      }

      $etime = substr($e['eventTime'], 0, -3);
      $age = $now - $etime;
      if ($age <= 3600) {
        return "dd0000fc"; // "red";
      } else if ($age <= 86400) {
        return "dd009dfe"; // "orange";
      } else if ($age <= 604800) {
        return "dd03fffb"; // "yellow";
      } else {
        return "ddffffff"; // "white";
      }
    } else {
      $depth = $e['eventDepth'];

      if ($depth <= 35) {
        return "dd009dfe"; //"orange";
      } else if ($depth <= 70) {
        return "dd03fffb"; //"yellow";
      } else if ($depth <= 150) {
        return "dd00ff00"; //"green";
      } else if ($depth <= 300) {
        return "ddfd0004"; //"blue";
      } else if ($depth <= 500) {
        return "ddc424a0"; //"purple";
      } else {
        return "dd0000fc"; //"red";
      }
    }
  }

  protected function getSize($e) {
    static $sizes = array(.2, .2, .3, .4, .5, .6, .8, 1.1, 1.5, 2.0, 2.5); // Mag 0 - 10

    $mag = $e['eventMagnitude'];
    if ($mag !== null && $mag != '') {
      $mag = intval($mag);
    } else {
      $mag = 0;
    }
    return $sizes[max(0, $mag)];
  }
}

?>
